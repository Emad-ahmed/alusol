document.addEventListener('DOMContentLoaded', function() {
    // Get references to the input fields and select elements
    const itemSelect = document.getElementById('item');
    const openYInput = document.getElementById('open_y');
    const closeNInput = document.getElementById('close_n');
    const mtrSelect = document.getElementById('mtr');
    const mtrYInput = document.getElementById('mtr_y');
    const mtrNInput = document.getElementById('mtr_n');
    const quantityInput = document.getElementById('quantity');
    const totalMeterInput = document.getElementById('total_meter');
    const searchInput = document.getElementById('searchInput');
    const bundleInput = document.getElementById('bundle');
    const priceInput = document.getElementById('price');
    const amountInput = document.getElementById('amount');

    console.log(quantityInput.value);

    // Function to calculate and set the values
    function calculateMeters() {
        // Check if the selected ITEM is "SLAT"
        if (itemSelect.value === 'SLAT') {
            // Get the selected mtr value and convert it to a number
            const mtrValue = parseFloat(mtrSelect.value);

            // Get the open_y and close_n values and convert them to numbers
            const openYValue = parseFloat(openYInput.value) || 0;
            const closeNValue = parseFloat(closeNInput.value) || 0;

            // Calculate mtr_y and mtr_n
            const mtrY = openYValue * mtrValue;
            const mtrN = closeNValue * mtrValue;

            // Set the calculated values to the respective fields
            mtrYInput.value = mtrY.toFixed(2); // Round to 2 decimal places
            mtrNInput.value = mtrN.toFixed(2); // Round to 2 decimal places

            // Calculate and set the quantity (open_y + close_n)
            const quantity = openYValue + closeNValue;
            quantityInput.value = quantity.toFixed(2); // Round to 2 decimal places

            // Calculate and set the total_meter (mtr_y + mtr_n)
            const totalMeter = mtrY + mtrN;
            totalMeterInput.value = totalMeter.toFixed(2); // Round to 2 decimal places

            // Call the calculateBundle function to update the bundle field
            calculateBundle();

            // Call the calculateAmount function to update the amount field
            calculateAmount();
        } else {
            // If the ITEM is not "SLAT", clear the fields
            mtrYInput.value = '';
            mtrNInput.value = '';
            quantityInput.value = '';
            totalMeterInput.value = '';
            bundleInput.value = ''; // Clear bundle field as well

            // Call the calculateAmount function to update the amount field
            calculateAmount();
        }
    }

    // Function to calculate and set the bundle value
    function calculateBundle() {
        // Check if the selected ITEM is "SLAT" and PRODUCT starts with "ALS 41"
        if (itemSelect.value === 'SLAT' && searchInput.value.startsWith('ALS 41')) {
            // Get the total_meter value and convert it to a number
            const totalMeterValue = parseFloat(totalMeterInput.value) || 0;

            // Calculate bundle (total_meter / 300)
            const bundleValue = totalMeterValue / 300;

            // Set the calculated value to the bundle field
            bundleInput.value = bundleValue.toFixed(2); // Round to 2 decimal places
        } else {
            // If conditions are not met, clear the bundle field
            bundleInput.value = '';
        }
    }

    // Function to calculate and set the amount value
    function calculateAmount() {
        // Get the total_meter, quantity, and price values
        const totalMeter = parseFloat(totalMeterInput.value) || 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;

        let amount = 0;

        // Calculate amount based on ITEM
        if (itemSelect.value === 'SLAT') {
            // If ITEM is "SLAT", calculate amount as total_meter * price
            amount = totalMeter * price;
        } else {
            // If ITEM is not "SLAT", calculate amount as quantity * price
            amount = quantity * price;
        }

        // Set the calculated amount to the amount field
        amountInput.value = amount.toFixed(2); // Round to 2 decimal places
    }

    // Add event listeners to the relevant input fields and select elements
    itemSelect.addEventListener('change', calculateMeters); // Check ITEM change
    openYInput.addEventListener('input', calculateMeters); // Check open_y input
    closeNInput.addEventListener('input', calculateMeters); // Check close_n input
    mtrSelect.addEventListener('change', calculateMeters); // Check mtr change
    searchInput.addEventListener('input', calculateBundle); // Check PRODUCT input
    totalMeterInput.addEventListener('input', calculateBundle); // Check total_meter input
    priceInput.addEventListener('input', calculateAmount); // Check price input
});