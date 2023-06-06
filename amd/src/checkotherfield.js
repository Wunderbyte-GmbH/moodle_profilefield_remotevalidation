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
 */
export const init = () => {
    const regex = /^[129]\d{13}$/;
    const mform = document.querySelector('[class="mform"]');
    let inputfield = document.querySelector('#id_profile_field_PIN');
    let parentfield = document.querySelector('#fitem_id_profile_field_PIN');
    if (regex.test(inputfield.value)) {
        inputfield.disabled = true;
        parentfield.style.display = 'none';
    }
    const dropdownSelector = '#id_profile_field_pincheck';
    let dropdown = document.querySelector(dropdownSelector);
    let initialDropownValue = dropdown.value;
    dropdown.addEventListener('change', () => {
        const selectedValue = dropdown.value;
        handleDropdownChange(selectedValue);
    });
    mform.addEventListener('submit', () => {
        if (regex.test(inputfield.value)) {
            dropdown.value = initialDropownValue;
        }
    });
};

/**
 * Add event listener on conditional profile field.
 * The value is the one from the conditional field.
 * If that contains a certain pattern in order to detect if a fake PIN should be created.
 * @param {string} value
 */
function handleDropdownChange(value) {
    // eslint-disable-next-line no-console
    // Check if PIN is already set and is valid.
    const regex = /^[129]\d{13}$/;
    let inputfield = document.querySelector('#id_profile_field_PIN');
    let parentfield = document.querySelector('#fitem_id_profile_field_PIN');
    if (value.toLowerCase().includes("not") && !regex.test(inputfield.value)) {
        inputfield.value = "nopin";
        parentfield.style.display = 'none';
    } else {
        parentfield.style.display = 'block';
        if (regex.test(inputfield.value)) {
            inputfield.disabled = true;
            parentfield.style.display = 'none';
        }
    }
}