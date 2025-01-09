import './bootstrap';
import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css'
window.bootstrap = bootstrap

import $ from 'jquery'
import select2 from 'select2'
select2();
window.$ = window.jQuery = $

// https://laravel.com/docs/11.x/vite#blade-processing-static-assets
import.meta.glob([
    '../images/**',
    '../css/**',
])

// https://select2.org/searching#matching-grouped-options
function select2MatchStart(params, data) {
    // If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
        return data;
    }

    // Skip if there is no 'children' property
    if (typeof data.children === 'undefined') {
        return null;
    }

    // `data.children` contains the actual options that we are matching against
    var filteredChildren = [];
    $.each(data.children, function (idx, child) {
        if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
            filteredChildren.push(child);
        }
    });

    // If we matched any of the timezone group's children, then set the matched children on the group
    // and return the group object
    if (filteredChildren.length) {
        var modifiedData = $.extend({}, data, true);
        modifiedData.children = filteredChildren;

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}

window.refreshSelect2 = function () {
    // při navigaci Zpět v prohlížeči se zobrazí neaktivní element a atributy dřívějšího select2, které před jeho opětovným obnovením musím smazat
    $('span.select2-container').remove();
    $('[data-select2-id]').removeAttr('data-select2-id').removeClass('select2-hidden-accessible');
    
    $('.select2:not(#page.sframe *)').each(function() {
        var cssClass = $(this).hasClass('form-select-sm') ? 'select2--small' : ''
        //if ($(this).hasClass("select2-hidden-accessible")) $(this).select2('destroy')
        $(this).select2({
            theme: "bootstrap-5",
            // https://stackoverflow.com/a/71552114/14967413
            dropdownParent: $(this).parent(),
            selectionCssClass: cssClass,
            dropdownCssClass: cssClass,
        })
    })
    
    $('.select2-register-names').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            // https://stackoverflow.com/a/71552114/14967413
            dropdownParent: $(this).parent(),
            createTag: function (params) {
                return {
                    id: params.term,
                    text: `${params.term} (vlastní nekategorizovaný rejstřík)`,
                    newTag: true
                };
              },
            templateResult: function (state) {
                if (!state.id || !state.element) return state.text
                var dataset = state.element.dataset
                // TODO: lokalizace
                if (!dataset.nonCustomRegister) {
                    var text = state.text.replace(' (vlastní nekategorizovaný rejstřík)', '')
                    return $(`
                        <span>
                            <span class="fw-bold">${text}</span>
                            <br />
                            <span class="fst-italic">vlastní nekategorizovaný rejstřík</span>
                        </span>
                    `)
                }
                else {
                    var categories = !dataset.categoriesList ? '' : dataset.categoriesList
                        .split(', ')
                        .map(categoryName => `<span class="badge text-bg-secondary">${categoryName}</span>`)
                        .join(' ')
                    var dot = ['Jazyky', 'Reeds'].includes(dataset.categoryName) ? '&bull; ' : ''
                    var pitches = ''
                    if (dataset.pitchesList != '') pitches = `<br />
                        Běžné polohy: <span class="fst-italic">${dataset.pitchesList}</span>`
                    return $(`
                        <span title="${dataset.description}">
                            <span class="float-end fst-italic">${dot}${dataset.defaultPitchLabel}</span>
                            <span class="fw-bold">${state.text}</span> <span class="text-body-secondary">(${dataset.language})</span>
                            <br />
                            <span>
                                <span class="badge text-bg-primary">${dataset.categoryName}</span>
                                ${categories}
                            </span>
                            ${pitches}
                        </span>
                    `)
                }
            }
        }).on('select2:close', function () {
            $('.pitch-select').select2('open')
        })    
    })
    
    $('.select2-pitch').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            // https://stackoverflow.com/a/71552114/14967413
            dropdownParent: $(this).parent(),
            // při matchingu se musí shodovat začátek, jinak by se např. pro "2'" našlo i "32'"
            matcher: select2MatchStart
        })
    });
    
    $('.pitch-select').on('select2:close', function () {
        $('.multiplier input').focus()
    })
}

window.refreshSelect2Sync = function (wire) {
    $(wire.$el).find('.select2:not(#page.sframe *), .select2-register-names, .select2-pitch').on('change', function () {
        var data = $(this).select2('val');
        // https://livewire.laravel.com/docs/properties#manipulating-properties
        var name = $(this).attr('wire:model.live') || $(this).attr('wire:model.change');
        if (name) wire.$set(name, data);
        var name = $(this).attr('wire:model');
        if (name) {
            //  - nestačí volat wire[name] = data
            //  - nuté zohlednit vnořené vlastnosti:
            //    je-li name 'form.property', musí se volat wire.form.property = data
            var wire1 = wire;
            var names = name.split('.')
            for (var i = 0; i < names.length; i++) {
                var name1 = names[i]
                if (i === names.length - 1) wire1[name1] = data
                else wire1 = wire1[name1]
            }
        }
    });
}

function refreshBootstrap() {
    // https://getbootstrap.com/docs/5.3/components/tooltips/#enable-tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl, { trigger : 'hover' }))
    
    // https://getbootstrap.com/docs/5.3/components/popovers/#enable-popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => bootstrap.Popover.getOrCreateInstance(popoverTriggerEl))
}

window.removeTooltips = function () {
    // prevence visících tooltipů
    $('.tooltip, .popover').remove()
    $('.dropdown-menu').removeClass('show')
}

function showThumbnailOrgan($wire, organId) {
    new bootstrap.Modal($('#organThumbnail')[0]).show()
    $wire.setThumbnailOrgan(organId)
}

window.initGoogleMap = function ($wire) {
    setTimeout(function () {
        const map = document.querySelector('gmp-map')
        const markers = document.querySelectorAll('gmp-advanced-marker');

        // zobrazení modalu řešeno v JS, protože kliknutí na mobilu funguje jen s událostí pointerdown (ne click)
        // a s pointerdown naopak není kompatibilní data-bs-toggle, proto modal aktivujeme v JS
        markers.forEach(marker => {
            marker.addEventListener('pointerdown', function () {
                let organId = marker.dataset.organId
                showThumbnailOrgan($wire, organId)
            })
            const infoWindow = new google.maps.InfoWindow({
                headerDisabled: true,
                content: marker.dataset.mapInfo
            });
            marker.addEventListener('mouseover', function () {
                infoWindow.open({
                    anchor: marker,
                    map
                })
            })
            marker.addEventListener('mouseout', function () {
                infoWindow.close()
            })
            
            if ($(marker).is('[data-near-coordinate]')) {
                let pin = new google.maps.marker.PinElement({ background: 'yellow' })
                marker.appendChild(pin.element)
            }
        });
        
        if ($(map).is('[data-use-map-clusters]')) {
            new markerClusterer.MarkerClusterer({
                map: map.innerMap,
                markers
            })
        }
    })
}

function getTimelineOptions(step, maxDate, orientationAxis, start, end)
{
    let options = {
        min: '1500-01-01',
        max: maxDate,
        showCurrentTime: false,
        timeAxis: { scale: 'year', step },
        dataAttributes: ['entityType', 'entityId', 'url', 'isWorkshop'],
        orientation: { axis: orientationAxis },
        order: function (item1, item2) {
            // varhany: podle data stavby
            if (item1.entityType === 'organ' && item2.entityType === 'organ') {
                return item2.start - item1.start
            }

            // nejprve varhanáři, pak varhany
            if (item1.entityType === 'organ') return -1
            if (item2.entityType === 'organ') return 1

            // varhanáři: podle data, timeline položky patřící stejnému varhanáři u sebe
            if (item1.entityId !== item2.entityId) return item2.entityId - item1.entityId
            if (item1.start.getTime() === item2.start.getTime()) return item2.end - item1.end
            return item2.start - item1.start
        },
        groupOrder: 'orderValue',
        xss: {
            filterOptions: {
                whiteList: {
                    span: ['class'],
                    i: ['class'],
                    h6: ['class'],
                }
            }
        },
        loadingScreenTemplate: function() {
            return '<h6 class="text-center">Načítání časové osy...</h6>'
        },
        template: function (item, element, data) {
            if (item.type === 'background') return ''
            
            var icon = item.entityType === 'organ' ? 'music-note-list' : 'person-circle'
            var timeClass = 'text-body-secondary';
            if (item.entityType !== 'organ') timeClass += ' small';
            var iconPrivate = (item.entityType === 'organBuilder' && !item.public) ? ' <i class="bi-lock text-warning"></i>' : ''
            return `<i class='bi-${icon} text-primary'></i> ${data.name}${iconPrivate} <span class='${timeClass}'>(${data.time})</span>`
        },
        groupTemplate: function (item) {
            if (item.content.startsWith('ž-')) return ''
            return item.content
        }
    }
    
    if (start) options.start = start;
    if (end) options.end = end;
    
    return options;
}

window.initTimeline = async function ($wire, timelineItems, timelineGroups, timelineMarkers) {
    // TODO: alternativní způsoby importu nejsou tak datově náročné (https://github.com/visjs/vis-timeline)
    const vis = await import('vis-timeline/standalone')
    
    setTimeout(function () {
        var container = $('#timeline')[0]
        
        var max = new Date()
        max.setFullYear(max.getFullYear() + 20)
        var step = parseInt(container.dataset.step)
        var orientationAxis = $(container).is('[data-axis-both]') ? 'both' : 'bottom'
        var start = $(container).data('start')
        var end = $(container).data('end')

        var options = getTimelineOptions(step, max, orientationAxis, start, end)
        
        timelineItems = timelineItems.map(function (item) {
            item.end ??= max
            if (item.entityType === 'organBuilder') {
                item.title = item.name
                if (item.time) item.title += ` (${item.time})`
            }
            return item
        })
        
        var items = new vis.DataSet(timelineItems)
        var timeline = new vis.Timeline(container, items, options)
        
        if (timelineGroups !== null) {
            var groups = new vis.DataSet()
            for (var key in timelineGroups) {
                groups.add({
                    id: timelineGroups[key].name,
                    orderValue: timelineGroups[key].orderValue,
                    content: timelineGroups[key].name,
                    nestedGroups: timelineGroups[key].nestedGroups
                })
            }
            timeline.setGroups(groups)
        }
        
        timelineMarkers.forEach(function (marker, i) {
            let id = `marker${i}`
            timeline.addCustomTime(marker.date, id)
            timeline.setCustomTimeMarker(marker.name, id)
            // TODO: title nastavit nejde
            timeline.setCustomTimeTitle(marker.description, id)
        })
        
        timeline.on('click', function ({ item }) {
            if (item) {
                var timelineItem = items.get(item);
                if (timelineItem.entityType === 'organ') {
                    window.open(timelineItem.url, '_blank')
                }
                else showThumbnailOrgan($wire, timelineItem.entityId)
            }
        })
        
        var selectedEntityType = container.dataset.selectedEntityType
        var selectedEntityId = container.dataset.selectedEntityId ? parseInt(container.dataset.selectedEntityId) : undefined
        if (selectedEntityType && selectedEntityId) {
            var ids = [];
            items.forEach(item => {
                if (item.entityType === selectedEntityType && item.entityId === selectedEntityId) {
                    ids.push(item.id)
                }
            })
            if (ids.length > 0) timeline.setSelection(ids)
        }
    })
}

window.scrollToTop = function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.scrollToElement = function (elem) {
    $(elem).get(0).scrollIntoView({ behavior: 'smooth' });
}

window.copyToClipboard = function (text) {
    return navigator.clipboard.writeText(text);
}

// TODO: toasty se překrývají, pokud jsou zobrazeny těsně za sebou
//  - lepší by bylo mít jen 1 HTML toast, volat ho JS funkcí a text toastu funkci předat jako argument
//  - při zobrazení dalšího toastu by se text předchozího toastu prostě jen přepsal (protože jde o jediný HTML element)
window.showToast = function (id) {
    var toast = $(`#${id}`)[0]
    var bootstrapToast = bootstrap.Toast.getOrCreateInstance(toast, { delay: 1750 })
    bootstrapToast.show()
}

if (typeof Livewire !== typeof undefined) {
    $(document)
        .on('livewire:init', () => {
            Livewire.on('select2-rendered', (e) => {
                setTimeout(() => {
                    refreshSelect2()
                })
            })
            Livewire.on('select2-sync-needed', ({ componentName }) => {
                setTimeout(() => {
                    var wire = Livewire.getByName(componentName)[0]
                    if (wire) refreshSelect2Sync(wire)
                    else console.error('select2-sync-needed: wire not found');
                })
            })
            Livewire.on('select2-open', ({ selector }) => {
                setTimeout(() => {
                    $(selector).select2('open');
                })
            })
            Livewire.on('select2-focus', ({ selector }) => {
                setTimeout(() => {
                    $(selector).select2('focus');
                })
            })
            Livewire.on('bootstrap-rendered', () => {
                setTimeout(() => {
                    refreshBootstrap()
                })
            })
        })
        .on('livewire:navigated', () => {
            console.log('navigated');
            removeTooltips()
            refreshSelect2()
            refreshBootstrap()
        })
    refreshBootstrap()

    // https://forum.laravel-livewire.com/t/using-livewire-with-select2-selectpicker/18/3
    Livewire.hook('component.init', ({ component }) => {
        $(function() {
            refreshSelect2Sync(component.$wire)
        })
    })
    Livewire.hook('commit', () => {
        removeTooltips()
    })
}
    