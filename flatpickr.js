document.addEventListener("DOMContentLoaded", function () {
    let fechasAusencias=[];
    let festivos = [];

    let tipoSeleccionado;
    
    fetch('https://datos.comunidad.madrid/catalogo/dataset/f160eb6c-6715-471e-9bc0-38497aae950f/resource/975f579d-92c2-42de-bfa9-aff5bd164586/download/festivos_regionales.json')
        .then(response => response.json())
        .then(json=> {
            let datosFestivos =[];

            if(Array.isArray(json.data)){
                datosFestivos = json.data;
            }else if(Array.isArray(json)){
                datosFestivos = json;
            }else{
                console.error("Estructura de datos inesperada: ", json);
                return;
            }

                festivos = datosFestivos.map(f =>{
                    if(!f.fecha_festivo) return null;
                    const [dia, mes, anio] = f.fecha_festivo.split('/');
                    return new Date(`${anio} - ${mes} - ${dia}`);
                }).filter(f => f !== null);
            actualizarColores(calendarioInferior);
        })
        .catch(error => console.error("Error al cargar festivos: ", error));
    
    fetch('https://datos.comunidad.madrid/catalogo/dataset/f160eb6c-6715-471e-9bc0-38497aae950f/resource/db6a3cb0-5504-4db8-9fe7-e42af1ae329b/download/festivos_locales.json')
        .then(response => response.json())
        .then(json =>{
            let datosFestivos = json.data;
            if(Array.isArray(json.data)){
                datosFestivos = json.data;
            }else if(Array.isArray(json)){
                datosFestivos = json;
            }else{
                console.error("Estructura de datos inesperada: ", json);
                return;
            }

            const festivosMadridCapital = datosFestivos.filter(f => f.municipio_codigo === "079");

            festivos = festivos.concat(festivosMadridCapital.map(f =>{
                if(!f.fecha_festivo) return null;
                const [dia, mes, anio] = f.fecha_festivo.split('/');
                return new Date (`${anio} - ${mes} - ${dia}`);
            }).filter(f=> f !== null));
            console.log("Festivos cargados: ", festivos.map(f => f.toDateStrting()));
            actualizarColores(calendarioInferior);
        })
        .catch(error => console.error("Error al cargar festivos: ", error));

    const calendarioSuperior= flatpickr("#dayChoosen", {
        altFormat: "j F Y",
        altInput: true,
        dateFormat: "Y-m-d",
        defaultDate: "today",
        locale: "es",
        showMonths: 1,
        showDaysInNextAndPreviousMonths: true,
        firstDayOfWeek: 0,
        onDayCreate : function(dObj, dStr, fp, dayElem){
            const date = dayElem.dateObj;
            const selected = fechasAusencias.find(f => f.date.toDateString() === date.toDateString());
            if(selected){
                switch (selected.tipo){
                    case "vacaciones":
                        dayElem.classList.add("vacaciones");
                        break;
                    case "medico":
                        dayElem.classList.add("medico");
                        break;
                    case "baja":
                        dayElem.classList.add("baja");
                        break;
                    case "otros": 
                    default:
                        dayElem.classList.add("otros");
                        break;
                }
            }

            const esFestivo = festivos.some(f=>
                f.getFullYear() === date.getFullYear() &&
                f.getMonth() === date.getMonth() &&
                f.getDate() === date.getDate()
            );

            if(esFestivo){
                dayElem.classList.add("festivo");
            }
        },
        onOpen: function(){
            calendarioSuperior.redraw();
        }
    });
    

        const calendarioInferior= flatpickr('#calendar',{
        defaultDate: new Date(),
        inline:true,
        enableTime: false,
        noCalendar: false,
        dateFormat: "d-m-Y",
        locale: "es",
        mode: "single",
        onReady: function(selectedDates, dateStr, instance) {
            actualizarColores(instance);
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
            actualizarColores(instance);
        },
        onYearChange: function(selectedDates, dateStr, instance) {
            actualizarColores(instance);
        },
        onChange: function(selectedDates){
            if(!tipoSeleccionado) return;
            selectedDates.forEach(date => {
                const existing = fechasAusencias.find(f=> f.date.toDateString() === date.toDateString());
                if(existing){
                    existing.tipo =tipoSeleccionado;
                }else{
                    fechasAusencias.push({date, tipo: tipoSeleccionado});
                }
            });
            
            actualizarColores(calendarioInferior);
            actualizarColores(calendarioSuperior);
            calendarioSuperior.redraw();
        },
        showMonths: 1,
        showDaysInNextAndPreviousMonths: true,
    });


    flatpickr("#clockOn1", {
        enableTime: true,
        noCalendar: true,
        time_24hr:true,
        locale: "es",
        defaultDate: "09:00"
    });

    flatpickr("#clockOn2", {
        enableTime: true,
        noCalendar: true,
        time_24hr:true,
        locale: "es",
        defaultDate: "15:00"
    });

    flatpickr("#clockOut1", {
        enableTime: true,
        noCalendar: true,
        time_24hr:true,
        locale: "es",
        defaultDate: "14:00"
    });

    flatpickr("#clockOut2", {
        enableTime: true,
        noCalendar: true,
        time_24hr:true,
        locale: "es",
        defaultDate: "18:00"
    });

    flatpickr('#restBegins',{ 
        enableTime: true,
        noCalendar: true,
        time_24hr:true,
        locale: "es",
        defaultDate: "14:00"
    });

    flatpickr('#restEnds',{
        enableTime: true,
        noCalendar: true,
        time_24hr:true,
        locale: "es",
        defaultDate: "15:00"
    });

    flatpickr('#date',{
        altFormat: "j F Y",
        altInput: true,
        dateFormat: "d-m-Y",
        maxDate: "today",
        locale: "es"
    });


    
    function actualizarColores(instance) {
        if(!instance) return;
        const dayElements = instance.calendarContainer?.querySelectorAll('.flatpickr-day');
        
        dayElements.forEach((dayElem) => {
            const dateStr =dayElem.getAttribute("aria-label");
            if (!dateStr) return;
            const dateObj = new Date(dateStr);

            const match = fechasAusencias.find(f =>
                f.date.getFullYear() === dateObj.getFullYear() &&
                f.date.getMonth() === dateObj.getMonth() &&
                f.date.getDate () === dateObj.getDate()
            );
    
            dayElem.classList.remove("vacaciones", "medico", "baja", "otros");
            
            if(match && match.tipo){
                dayElem.classList.add(match.tipo);
            }

            const esFestivo= festivos.some(f=>
                f.getFullYear() === dateObj.getFullYear() &&
                f.getMonth() === dateObj.getMonth() &&
                f.getDate() === dateObj.getDate()
            );

            if(esFestivo){
                dayElem.classList.add("festivo");
            }

            if (!dayElem.classList.contains("color-listener")) {
                dayElem.addEventListener("click", function () {
                    const fechaStr = dateObj.toDateString();

                    const index = fechasAusencias.findIndex(f =>
                        f.date.getFullYear() === dateObj.getFullYear() &&
                        f.date.getMonth() === dateObj.getMonth() &&
                        f.date.getDate () === dateObj.getDate()
                    );

                    if(index === -1){
                        fechasAusencias.push({date: new Date(dateObj), tipo: "vacaciones"});
                    }else{
                        const tipos = ["vacaciones", "medico", "baja", "otros"];
                        const actual = fechasAusencias[index].tipo;
                        const nextTipoIndex = tipos.indexOf(actual) + 1;

                        if(nextTipoIndex >= tipos.length){
                            fechasAusencias.splice(index,1);
                        }else{
                            fechasAusencias[index].tipo = tipos[nextTipoIndex];
                        }
                    }
                    calendarioSuperior.redraw();
                    if(typeof calendarioInferior !== "undefined"){
                        actualizarColores(calendarioInferior);
                    }
                });
    
                dayElem.classList.add("color-listener");
            }
        });
    }


});
