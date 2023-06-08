// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Add event listener on conditional profile field
 * @param {int} isadmin 0 if not admin 1 if admin
 */
export const init = (isadmin) => {
    // Regex to check of the entered data is in the format of the PIN.
    const regex = /^[12]\d{13}$/;
    const regexfake = /^9\d{13}$/;
    const mform = document.querySelector('[role="main"] [class="mform"], .signupform .mform');
    // The input field where the PIN is set by the user.
    let inputfield = document.querySelector('#id_profile_field_PIN');
    // The container of the input field that is going to be shown or hiden.
    let parentfield = document.querySelector('#fitem_id_profile_field_PIN');
    // Hide the field when the PIN is already there. Only admins can view and edit the field.
    if (regex.test(inputfield.value) && !isadmin) {
        inputfield.disabled = true;
        parentfield.style.display = 'none';
    }
    let dropdown = document.querySelector('#id_profile_field_pincheck');
    let initialDropownValue = dropdown.value;
    dropdown.addEventListener('change', () => {
        const selectedValue = dropdown.value;
        handleDropdownChange(selectedValue, inputfield, parentfield, regex, regexfake, isadmin);
    });
    mform.addEventListener('submit', () => {
        if (regex.test(inputfield.value) && !isadmin) {
            dropdown.value = initialDropownValue;
        }
    });
};

/**
 *
 * Add event listener on conditional profile field.
 * The selectedValue is the one from the conditional field.
 * If that contains a certain pattern in order to detect if a fake PIN should be created.
 *
 * @param {string} selectedValue
 * @param {Element} inputfield
 * @param {Element} parentfield
 * @param {RegExp} regex
 * @param {RegExp} regexfake
 * @param {int} isadmin
 * */
function handleDropdownChange(selectedValue, inputfield, parentfield, regex, regexfake, isadmin) {
    // Check if PIN is already set and is valid.
    if (selectedValue.toLowerCase().includes("not") && !regexfake.test(inputfield.value)) {
        inputfield.value = "nopin";
        parentfield.style.display = 'none';
    } else {
        parentfield.style.display = 'block';
        if (regexfake.test(inputfield.value) && !isadmin) {
            inputfield.disabled = true;
            parentfield.style.display = 'none';
        }
        if (inputfield.value === "nopin") {
            inputfield.value = "";
        }
    }
}