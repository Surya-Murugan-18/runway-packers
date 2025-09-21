// Form state management
let currentStep = 1;
let selectedPackageType = "";
let selectedServiceType = "";
let selectedDeliverySpeed = "";

// DOM elements
const steps = document.querySelectorAll(".step");
const sections = document.querySelectorAll(".form-section");
const form = document.getElementById("courier-form");

// Navigation buttons
const nextBtn1 = document.getElementById("nextBtn1");
const nextBtn2 = document.getElementById("nextBtn2");
const prevBtn2 = document.getElementById("prevBtn2");
const prevBtn3 = document.getElementById("prevBtn3");
const resetBtn = document.getElementById("resetBtn");
const submitBtn = document.getElementById("submitBtn");

// Selection elements
const packageTypes = document.querySelectorAll(".package-types .package-type");
const serviceTypes = document.querySelectorAll(".service-types .package-type");
const deliveryOptions = document.querySelectorAll(".delivery-option");

// Initialize form
document.addEventListener("DOMContentLoaded", () => {
  setupEventListeners();
  setMinDate();
});

function setupEventListeners() {
  nextBtn1.addEventListener("click", () => validateAndNextStep(1));
  nextBtn2.addEventListener("click", () => validateAndNextStep(2));
  prevBtn2.addEventListener("click", () => goToStep(1));
  prevBtn3.addEventListener("click", () => goToStep(2));
  resetBtn.addEventListener("click", resetForm);
  form.addEventListener("submit", handleSubmit);

  const okBtn = document.getElementById("okBtn");
  if (okBtn) {
    okBtn.addEventListener("click", () => {
      document.getElementById("successModal").style.display = "none";
      resetForm();
      window.location.href = "index.html"; // redirect to homepage
    });
  }

  packageTypes.forEach((type) => {
    type.addEventListener("click", function () {
      selectPackageType(this.dataset.type);
    });
  });

  serviceTypes.forEach((type) => {
    type.addEventListener("click", function () {
      selectServiceType(this.dataset.service);
    });
  });

  deliveryOptions.forEach((option) => {
    option.addEventListener("click", function () {
      selectDeliverySpeed(this.dataset.speed);
    });
  });
}

function setMinDate() {
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("pickupDate").setAttribute("min", today);
}

function validateStep(step) {
  const errors = [];

  if (step === 1) {
    const requiredFields = [
      "senderName",
      "senderEmail",
      "senderPhone",
      "pickupDate",
      "pickupAddress",
      "receiverName",
      "receiverPhone",
      "deliveryAddress",
    ];

    requiredFields.forEach((fieldId) => {
      const field = document.getElementById(fieldId);
      const formGroup = field.closest(".form-group");

      if (!field.value.trim()) {
        showFieldError(formGroup, `${getFieldLabel(fieldId)} is required`);
        errors.push(fieldId);
      } else {
        clearFieldError(formGroup);
        if (fieldId === "senderEmail" && !isValidEmail(field.value)) {
          showFieldError(formGroup, "Please enter a valid email address");
          errors.push(fieldId);
        }
      }
    });
  }

  if (step === 2) {
    const requiredFields = ["packageWeight", "numberOfPackages", "shipmentMode"];

    requiredFields.forEach((fieldId) => {
      const field = document.getElementById(fieldId);
      const formGroup = field.closest(".form-group");

      if (!field.value.trim()) {
        showFieldError(formGroup, `${getFieldLabel(fieldId)} is required`);
        errors.push(fieldId);
      } else {
        clearFieldError(formGroup);
      }
    });

    if (!selectedPackageType) {
      showError("Please select a package type");
      errors.push("packageType");
    }

    if (!selectedServiceType) {
      showError("Please select a service type");
      errors.push("serviceType");
    }

    if (!selectedDeliverySpeed) {
      showError("Please select a delivery speed");
      errors.push("deliverySpeed");
    }
  }

  return errors.length === 0;
}

function validateAndNextStep(step) {
  if (validateStep(step)) {
    if (step === 2) {
      populateReviewStep();
    }
    goToStep(step + 1);
  }
}

function goToStep(step) {
  currentStep = step;
  steps.forEach((stepEl, index) => {
    stepEl.classList.toggle("active", index + 1 === step);
  });

  sections.forEach((section, index) => {
    section.classList.toggle("active", index + 1 === step);
  });
}

function selectPackageType(type) {
  selectedPackageType = type;
  packageTypes.forEach((el) => {
    el.classList.toggle("selected", el.dataset.type === type);
  });
}

function selectServiceType(type) {
  selectedServiceType = type;
  serviceTypes.forEach((el) => {
    el.classList.toggle("selected", el.dataset.service === type);
  });
}

function selectDeliverySpeed(speed) {
  selectedDeliverySpeed = speed;
  deliveryOptions.forEach((el) => {
    el.classList.toggle("selected", el.dataset.speed === speed);
  });
}

function populateReviewStep() {
  document.getElementById("reviewSenderName").textContent = document.getElementById("senderName").value;
  document.getElementById("reviewSenderEmail").textContent = document.getElementById("senderEmail").value;
  document.getElementById("reviewSenderPhone").textContent = document.getElementById("senderPhone").value;
  document.getElementById("reviewPickupDate").textContent = formatDate(document.getElementById("pickupDate").value);
  document.getElementById("reviewPickupAddress").textContent = document.getElementById("pickupAddress").value;

  document.getElementById("reviewReceiverName").textContent = document.getElementById("receiverName").value;
  document.getElementById("reviewReceiverPhone").textContent = document.getElementById("receiverPhone").value;
  document.getElementById("reviewDeliveryAddress").textContent = document.getElementById("deliveryAddress").value;

  document.getElementById("reviewWeight").textContent = document.getElementById("packageWeight").value;
  document.getElementById("reviewPackages").textContent = document.getElementById("numberOfPackages").value;
  document.getElementById("reviewPickup").textContent = getPickupTypeLabel(document.getElementById("shipmentMode").value);
  document.getElementById("reviewPackageType").textContent = getPackageTypeLabel(selectedPackageType);
  document.getElementById("reviewServiceType").textContent = getServiceTypeLabel(selectedServiceType);
  document.getElementById("reviewDeliverySpeed").textContent = getDeliverySpeedLabel(selectedDeliverySpeed);
}

async function handleSubmit(e) {
  e.preventDefault();

  if (!validateStep(1) || !validateStep(2)) {
    showError("Please fill in all required fields");
    return;
  }

  submitBtn.disabled = true;
  submitBtn.textContent = "Submitting...";

  const formData = {
    sender_name: document.getElementById("senderName").value,
    sender_email: document.getElementById("senderEmail").value,
    sender_phone: document.getElementById("senderPhone").value,
    pickup_date: document.getElementById("pickupDate").value,
    pickup_address: document.getElementById("pickupAddress").value,
    receiver_name: document.getElementById("receiverName").value,
    receiver_phone: document.getElementById("receiverPhone").value,
    delivery_address: document.getElementById("deliveryAddress").value,
    package_weight: Number.parseFloat(document.getElementById("packageWeight").value),
    number_of_packages: Number.parseInt(document.getElementById("numberOfPackages").value),
    pickup_type: document.getElementById("shipmentMode").value,
    package_type: selectedPackageType,
    service_type: selectedServiceType,
    delivery_speed: selectedDeliverySpeed,
  };

  try {
    const response = await fetch("api/submit_booking.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    });

    const result = await response.json();

    if (result.success) {
      showSuccessMessage(result.order_id);
    } else {
      showError(result.message || "Failed to submit booking. Please try again.");
    }
  } catch (error) {
    console.error("Error:", error);
    showError("Network error. Please check your connection and try again.");
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = "Confirm & Submit";
  }
}

function showSuccessMessage(orderId) {
  document.getElementById("orderIdText").innerHTML = `<strong>${orderId}</strong>`;
  document.getElementById("successModal").style.display = "flex";

  const tickVideo = document.getElementById("tickVideo");
  tickVideo.currentTime = 0;
  tickVideo.play();
}

function resetForm() {
  form.reset();
  selectedPackageType = "";
  selectedServiceType = "";
  selectedDeliverySpeed = "";

  packageTypes.forEach((el) => el.classList.remove("selected"));
  serviceTypes.forEach((el) => el.classList.remove("selected"));
  deliveryOptions.forEach((el) => el.classList.remove("selected"));

  document.querySelectorAll(".form-group").forEach((group) => {
    clearFieldError(group);
  });

  goToStep(1);

  document.getElementById("submissionSuccess").style.display = "none";
  document.querySelector(".review-section").style.display = "block";
  document.querySelector(".form-navigation").style.display = "flex";
}

// Utility Functions
function showFieldError(formGroup, message) {
  formGroup.classList.add("error");
  let errorEl = formGroup.querySelector(".error-message");
  if (!errorEl) {
    errorEl = document.createElement("div");
    errorEl.className = "error-message";
    formGroup.appendChild(errorEl);
  }
  errorEl.textContent = message;
  errorEl.style.display = "block";
}

function clearFieldError(formGroup) {
  formGroup.classList.remove("error");
  const errorEl = formGroup.querySelector(".error-message");
  if (errorEl) {
    errorEl.style.display = "none";
  }
}

function showError(message) {
  alert(message);
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

function getFieldLabel(fieldId) {
  const labels = {
    senderName: "Sender Name",
    senderEmail: "Sender Email",
    senderPhone: "Sender Phone",
    pickupDate: "Pickup Date",
    pickupAddress: "Pickup Address",
    receiverName: "Receiver Name",
    receiverPhone: "Receiver Phone",
    deliveryAddress: "Delivery Address",
    packageWeight: "Package Weight",
    numberOfPackages: "Number of Packages",
    shipmentMode: "Pickup Type",
  };
  return labels[fieldId] || fieldId;
}

function getPickupTypeLabel(type) {
  const types = {
    peerToPeer: "Peer to Peer",
    doorToDoor: "Door to Door",
  };
  return types[type] || "N/A";
}

function getPackageTypeLabel(type) {
  const types = {
    document: "Document",
    parcel: "Parcel",
    fragile: "Fragile",
    perishable: "Perishable",
    other: "Other",
  };
  return types[type] || "N/A";
}

function getServiceTypeLabel(type) {
  const types = {
    air: "By Air",
    train: "By Train",
    road: "By Road",
  };
  return types[type] || "N/A";
}

function getDeliverySpeedLabel(speed) {
  const speeds = {
    express: "Express Delivery",
    standard: "Standard Delivery",
  };
  return speeds[speed] || "N/A";
}

