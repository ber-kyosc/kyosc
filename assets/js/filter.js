import '../styles/filter.scss';
import noUiSlider from 'nouislider';
import moment from 'moment';

const { $ } = window;
$(document).ready(() => {
    $('input[name="dateStart"]').daterangepicker({
        autoUpdateInput: false,
        timePicker: false,
        showDropdowns: true,
        startDate: moment(),
        endDate: moment().startOf('hour').add(6, 'day'),
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Valider',
            cancelLabel: 'Annuler',
            fromLabel: 'De',
            toLabel: 'à',
            customRangeLabel: 'Custom',
            daysOfWeek: [
                'Dim',
                'Lun',
                'Mar',
                'Mer',
                'Jeu',
                'Ven',
                'Sam',
            ],
            monthNames: [
                'Janvier',
                'Février',
                'Mars',
                'Avril',
                'Mai',
                'Juin',
                'Juillet',
                'Août',
                'Septembre',
                'Octobre',
                'Novembre',
                'Décembre',
            ],
            firstDay: 1,
        },
    });
    // eslint-disable-next-line func-names
    $('input[name="dateStart"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(`${picker.startDate.format('DD/MM/YYYY')} - ${picker.endDate.format('DD/MM/YYYY')}`);
    });

    // eslint-disable-next-line func-names
    $('input[name="dateStart"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
});

function sliderAll(sliderTextParam, sliderImageParam) {
    const sliderText = sliderTextParam;
    const sliderImage = sliderImageParam;
    let minSelected = parseInt(sliderImage.dataset.min, 10);
    let maxSelected = parseInt(sliderImage.dataset.max, 10);
    if (sliderText.value !== '') {
        const value = sliderText.value.split('-');
        minSelected = parseInt(value[0].trim(), 10);
        maxSelected = parseInt(value[1].trim(), 10);
    }
    const minValue = parseInt(sliderImage.dataset.min, 10);
    const maxValue = parseInt(sliderImage.dataset.max, 10);
    const range = noUiSlider.create(sliderImage, {
        start: [minSelected, maxSelected],
        connect: true,
        step: 1,
        range: {
            min: minValue,
            max: maxValue,
        },
    });

    range.on('slide', (values) => {
        sliderText.value = `${Math.round(values[0])} - ${Math.round(values[1])}`;
    });
    range.on('end', () => {
        sliderText.dispatchEvent(new Event('change'));
    });
    sliderImage.style.display = 'none';
    sliderText.addEventListener('click', () => {
        if (getComputedStyle(sliderImage).display !== 'block') {
            sliderImage.style.display = 'block';
        } else {
            sliderImage.style.display = 'none';
        }
    });
}

/* Slider Participants */
const participants = document.getElementById('sliderParticipants');
if (participants) {
    const inputParticipants = document.getElementById('participants');
    sliderAll(inputParticipants, participants);
}

/* Slider Distance */
const distance = document.getElementById('sliderDistance');
if (distance) {
    const inputDistance = document.getElementById('distance');
    sliderAll(inputDistance, distance);
}
