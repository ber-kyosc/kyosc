document.addEventListener('DOMContentLoaded', () => {
    const cookieConsent = document.querySelector('.ch-cookie-consent');
    const cookieConsentForm = document.querySelector('.ch-cookie-consent__form');
    const cookieConsentFormBtn = document.querySelectorAll('.ch-cookie-consent__btn');
    const cookieConsentCategoryDetails = document.querySelector('.ch-cookie-consent__category-group');
    const cookieConsentCategoryDetailsToggle = document.querySelector('.ch-cookie-consent__toggle-details');

    // eslint-disable-next-line max-len
    // If cookie consent is direct child of body, assume it should be placed on top of the site pushing down the rest of the website
    if (cookieConsent && cookieConsent.parentNode.nodeName === 'BODY') {
        if (cookieConsent.classList.contains('ch-cookie-consent--top')) {
            document.body.style.marginTop = `${cookieConsent.offsetHeight}px`;

            cookieConsent.style.position = 'absolute';
            cookieConsent.style.top = '0';
            cookieConsent.style.left = '0';
        } else {
            document.body.style.marginBottom = `${cookieConsent.offsetHeight}px`;

            cookieConsent.style.position = 'fixed';
            cookieConsent.style.bottom = '0';
            cookieConsent.style.left = '0';
        }
    }

    if (cookieConsentForm) {
        // Submit form via ajax
        cookieConsentFormBtn.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();

                const xhr = new XMLHttpRequest();
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        cookieConsent.style.display = 'none';
                        const buttonEvent = new CustomEvent('cookie-consent-form-submit-successful', {
                            detail: event.target,
                        });
                        document.dispatchEvent(buttonEvent);
                    }
                };
                xhr.open('POST', cookieConsentForm.action);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                // eslint-disable-next-line no-use-before-define
                xhr.send(serializeForm(cookieConsentForm, event.target));

                // Clear all styles from body to prevent the white margin at the end of the page
                document.body.style.marginBottom = null;
                document.body.style.marginTop = null;
            }, false);
        });
    }

    if (cookieConsentCategoryDetails && cookieConsentCategoryDetailsToggle) {
        cookieConsentCategoryDetailsToggle.addEventListener('click', () => {
            const detailsIsHidden = cookieConsentCategoryDetails.style.display !== 'block';
            cookieConsentCategoryDetails.style.display = detailsIsHidden ? 'block' : 'none';
            cookieConsentCategoryDetailsToggle.querySelector('.ch-cookie-consent__toggle-details-hide').style.display = detailsIsHidden ? 'block' : 'none';
            cookieConsentCategoryDetailsToggle.querySelector('.ch-cookie-consent__toggle-details-show').style.display = detailsIsHidden ? 'none' : 'block';
        });
    }
});

function serializeForm(form, clickedButton) {
    const serialized = [];

    for (let i = 0; i < form.elements.length; i++) {
        const field = form.elements[i];

        if ((field.type !== 'checkbox' && field.type !== 'radio' && field.type !== 'button') || field.checked) {
            serialized.push(`${encodeURIComponent(field.name)}=${encodeURIComponent(field.value)}`);
        }
    }

    serialized.push(`${encodeURIComponent(clickedButton.getAttribute('name'))}=`);

    return serialized.join('&');
}
