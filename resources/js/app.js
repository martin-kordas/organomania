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

function isFullYearTimelineItem(item) {
    return (
        item.start.getDate() === 1 && item.start.getMonth() === 0
        && item.end.getDate() === 31 && item.end.getMonth() === 11
    )
}

function getTimelineOptions(container) {
    let options = {
        min: container.dataset.min,
        max: container.dataset.max,
        showCurrentTime: false,
        showMajorLabels: container.dataset.scale !== 'month',
        timeAxis: {
            scale: container.dataset.scale,
            step: parseInt(container.dataset.step)
        },
        dataAttributes: ['entityType', 'entityId', 'url', 'isWorkshop'],
        orientation: { axis: container.dataset.axis },
        order: function (item1, item2) {
            if (item1.entityType === 'festival' && item2.entityType === 'festival') {
                if (isFullYearTimelineItem(item1) && !isFullYearTimelineItem(item2)) return -1
                return item2.name.localeCompare(item1.name)
            }
            
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
            
            var icon
            if (item.entityType === 'organ') icon = 'music-note-list'
            else if (item.entityType === 'organBuilder') icon = 'person-circle'
            else icon = 'calendar-date'
            
            var detailsClass = 'text-body-secondary';
            if (item.entityType !== 'organ') detailsClass += ' small';
            var iconPrivate = (item.entityType === 'organBuilder' && !item.public) ? ' <i class="bi-lock text-warning"></i>' : ''
            
            var tmpl = `<i class='bi-${icon} text-primary'></i> ${data.name}${iconPrivate}`
            if (data.details) tmpl += ` <span class='${detailsClass}'>(${data.details})</span>`;
            return tmpl;
        },
        groupTemplate: function (item) {
            if (item.content.startsWith('ž-')) return ''
            return item.content
        }
    }
    
    if (container.dataset.start) options.start = container.dataset.start;
    if (container.dataset.end) options.end = container.dataset.end;
    
    if (container.dataset.scale === 'month') {
        options.format = {
            minorLabels: (date) => {
                // standardní lokalizace s využitím moment.js nefungovala (https://visjs.github.io/vis-timeline/docs/timeline/#Localization)
                return date.toDate().toLocaleString(navigator.language, { month: "short" })
            }
        }
    }
    
    return options;
}

window.initTimeline = async function ($wire, timelineItems, timelineGroups, timelineMarkers) {
    Promise.all([
        import('vis-data/peer'),
        import('vis-timeline/peer'),
        import('vis-timeline/styles/vis-timeline-graph2d.css'),
    ]).then(([visData, visTimeline]) => {
        var container = $('#timeline')[0]
        var options = getTimelineOptions(container)
        
        timelineItems = timelineItems.map(function (item) {
            item.end ??= container.dataset.max
            if (item.entityType === 'organBuilder' || item.entityType === 'festival') {
                item.title = item.name
                if (item.details) item.title += ` (${item.details})`
            }
            return item
        })
        
        var items = new visData.DataSet(timelineItems)
        var timeline = new visTimeline.Timeline(container, items, options)
        
        if (timelineGroups !== null) {
            var groups = new visData.DataSet()
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

window.initChart = async function ($wire, chartItems, texts) {
    const { default: ApexCharts } = await import('apexcharts')

    let manualsCountHidden = true
    let stopsCountHidden = true
    let originalStopsCountHidden = true
    let sortColumn = new URLSearchParams(window.location.search).get('sortColumn');
    if (sortColumn === 'manuals_count') manualsCountHidden = false
    else if (sortColumn === 'original_stops_count') originalStopsCountHidden = false
    else stopsCountHidden = false;

    let options = {
        series: [
            {
                name: texts.manualsCount,
                data: chartItems.series.manualsCount,
                hidden: manualsCountHidden,
                color: 'var(--bs-secondary)',
            },
            {
                name: texts.originalManualsCount,
                data: chartItems.series.originalManualsCount,
                hidden: true,
                color: 'rgb(149, 161, 172)',
            },
            {
                name: texts.stopsCount,
                data: chartItems.series.stopsCount,
                hidden: stopsCountHidden,
                color: 'var(--bs-primary)',
            },
            {
                name: texts.originalStopsCount,
                data: chartItems.series.originalStopsCount,
                hidden: originalStopsCountHidden,
                color: 'rgb(189, 127, 39)',
            },
        ],
        chart: {
            type: 'bar',
            height: `${chartItems.categories.length * 50 + 70}px`,
            fontFamily: 'inherit',
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function (_event, _chartContext, opts) {
                    //if (['mouseup', 'touchend'].includes(event.type)) {
                        if (opts.dataPointIndex >= 0) {
                            let organData = chartItems.organData[opts.dataPointIndex]
                            let organId = organData.id
                            showThumbnailOrgan($wire, organId)
                        }
                    }
                //}
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: 'end',
                horizontal: true,
                dataLabels: {
                    position: 'top',
                },
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: -20,
            formatter: function (_val, opt) {
                let organData = chartItems.organData[opt.dataPointIndex]
                let originalSeries = [1, 3].includes(opt.seriesIndex)
                let sizeInfo = originalSeries ? organData.originalSizeInfo : organData.sizeInfo
                return sizeInfo ?? '?'
            },
        },
        legend: {
            position: 'top',
            fontSize: '13px',
            markers: {
                size: 10
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            x: {
                formatter: function ([municipality, place, _shortPlace, organBuilderName, _organBuilderShortName, yearBuilt]) {
                    let text = `<strong>${municipality}</strong> | ${place}<br />${organBuilderName}`
                    if (yearBuilt) text += ` <span class='text-body-secondary'>(${yearBuilt})</span>`
                    return text
                }
            },
            y: {
                formatter: val => val
            }
        },
        xaxis: {
            categories: chartItems.categories,
            labels: {
                formatter: function (value) {
                    // desetinná čísla nezobrazujeme
                    let isDecimal = value % 1 != 0
                    return isDecimal ? '' : value
                }
            }
        },
        // TODO: title labelů osy Y je kostrbatým zřetězením, změnit lze ale jen obtížně (srv. https://chatgpt.com/share/67a29de0-ddbc-8012-9548-0c346a2bc02b - konec)
        yaxis: {
            labels: {
                style: {
                    fontSize: '13px',
                },
                offsetY: 5,
                maxWidth: $(window).width() / 3,
                formatter: function (val) {
                    if (Array.isArray(val)) {
                        let [municipality, _place, shortPlace, _organBuilderName, organBuilderShortName, yearBuilt] = val
                        
                        let line1 = `${municipality.toUpperCase()}, ${shortPlace}`
                        let line2 = organBuilderShortName
                        if (yearBuilt) line2 += `, ${yearBuilt}`
                        
                        return [line1, line2]
                    }
                }
            }
        }
    };

    var chart = new ApexCharts($("#chart")[0], options)
    chart.render()
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

function initFacebook() {
    // při prvním načtení stránky obvykle ještě není objekt FB dostupný, ale v takovém případě se plugin načte automaticky už includováním skriptu v <head>
    var elem = $('#fbPage')
    if (!elem.find('iframe').length) window.FB?.XFBML?.parse(elem[0])
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
            initFacebook()
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
    