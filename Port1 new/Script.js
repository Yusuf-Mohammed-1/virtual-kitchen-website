function validateForm() {
    const email = document.getElementById('email').value;
    const confirmEmail = document.getElementById('confirmEmail').value;
    const appointmentDate = document.getElementById('appointmentDate').value;
    const phoneInput = document.getElementById('phone').value;
    const phonePattern = /^0\d{10}$/; // This is a regex for 11 digits starting with 0

    // This is a validation to ensure a real phone number is entered 
    if (!phonePattern.test(phoneInput)) {
        alert("Please enter a valid phone number.");
        return false; // This prevents the form from submitting
    }

    // This checks if emails match
    if (email !== confirmEmail) {
        alert("Emails do not match!");
        return false;
    }

    // This checks to see if the email is a valid Aston University email 
    if (!checkEmails(email)) {
        alert("Please enter a valid Aston University email.");
        return false;
    }

    // This code checks to see if the appointment date is in the future
    if (!checkDate(appointmentDate)) {
        alert("Please select a future date for your appointment.");
        return false;
    }

    // It is true if all validations pass
    return true;
}

function checkEmails(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@(aston\.ac\.uk|student\.aston\.ac\.uk)$/;
    return regex.test(email);
}

function checkDate(date) {
    const selectedDate = new Date(date);
    const today = new Date();
    // This sets the time of today to midnight to only compare dates
    today.setHours(0, 0, 0, 0);
    return selectedDate > today; // This checks to see if the selected date is in the future
}