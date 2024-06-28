import './bootstrap';
import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css'

//this.livewire.hook('afterDomUpdate', (component) => { console.log("zdar"); })


function refreshSelect2() {
    $('.select2').each(function() {
        $(this).select2({
              theme: "bootstrap-5",
              // https://stackoverflow.com/a/71552114/14967413
              dropdownParent: $(this).parent()
        })
    })
}

function refreshBootstrap() {
    // https://getbootstrap.com/docs/5.3/components/tooltips/#enable-tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    
    // https://getbootstrap.com/docs/5.3/components/popovers/#enable-popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
}


import $ from 'jquery'
import select2 from 'select2'
select2();

$(document)
    .on('livewire:init', () => {
        Livewire.on('select2-rendered', () => {
            setTimeout(() => {
                refreshSelect2()
            })
        })
        Livewire.on('bootstrap-rendered', () => {
            setTimeout(() => {
                refreshBootstrap()
            })
        })
    })
    .on('livewire:navigated', () => {
        refreshSelect2()
        refreshBootstrap()
    })
refreshBootstrap()

// https://forum.laravel-livewire.com/t/using-livewire-with-select2-selectpicker/18/3
Livewire.hook('component.init', ({ component }) => {
    $(document).ready(function() {
        $(component.el).find('.select2').on('change', function (e) {
            var data = $(this).select2('val');
            // https://livewire.laravel.com/docs/properties#manipulating-properties
            var name = $(this).attr('wire:model.live');
            if (name) component.$wire.$set(name, data);
            var name = $(this).attr('wire:model');
            if (name) component.$wire[name] = data;
        });
    });
})

/*$('.modal').each(function () {
    this.addEventListener('shown.bs.modal', () => refreshSelect2())
})*/

// https://laravel.com/docs/11.x/vite#blade-processing-static-assets
import.meta.glob([
    '../images/**',
    '../css/**',
])


