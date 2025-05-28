document.addEventListener("DOMContentLoaded", function () { 
    
    function initializeFlatpickr() {
        document.querySelectorAll('.time-picker').forEach(input => {
            flatpickr(input, {
                enableTime: true,
                noCalendar: true,
                time_24hr:true,
                locale: "es",
                dateFormat: "H:i"
            });
    });
    }
    initializeFlatpickr();

    const addBtn = document.querySelector(".addButton");

    function updateAddButtonState(){
        const tbody = document.querySelector("#table tbody");
        const numFilas = tbody ? tbody.querySelectorAll("tr").length : 0;
        addBtn.disabled = numFilas >= 4;
    }

    document.querySelector(".addButton").addEventListener("click", function () {
        const tbody = document.querySelector("#table tbody");
        if(!tbody) return;
        const numFilas = tbody.querySelectorAll("tr").length;

        if(numFilas <4){
            let newRow = document.createElement("tr");
            newRow.classList.add("newRow");
            newRow.innerHTML = `<td><input type="text" class="time-picker clockOn4" data-input required></td>` +
                                `<td><input type="text" class="time-picker clockOut4" data-input required></td>` +
                                `<td><select name="category4" class="opctionChoosenCategory"><option value="1"> 👨🏽‍💼 Trabajo</option><option value="2"> ☕ Descanso</option></select></td>` +
                                `<td><select name="location4" class="opctionChoosenLocation"><option value="1"> 🏢 Oficina</option><option value="2"> 🏠 Casa</option></select></td>` +
                                `<td><input type="button" class="removeRowBtn" value="🗑️"></td>`;
            tbody.appendChild(newRow);
            initializeFlatpickr();
            updateAddButtonState();
        }
    });

    document.querySelector("#table tbody").addEventListener("click", function (e){
        if (e.target && e.target.classList.contains("removeRowBtn")) {
            const row = e.target.closest("tr");
            if (row) {
                row.remove();
                updateAddButtonState();
            }
        }
        updateAddButtonState();
    });



    document.querySelector(".saveButton").addEventListener("click", function(e){
    let dayTime =document.querySelector(".dayTime");
    let clockOn1 = document.querySelector("#clockOn1").value;
    let clockOut1 = document.querySelector("#clockOut1").value;
    let restBegins = document.querySelector("#restBegins").value;
    let restEnds = document.querySelector("#restEnds").value;
    let clockOn2 = document.querySelector("#clockOn2").value;
    let clockOut2 = document.querySelector("#clockOut2").value;
    let clockOn = document.querySelectorAll(".clockOn4");
    let clockOut = document.querySelectorAll(".clockOut4");
        function timeToMinutes (time){
            if(!time) return 0;
            let[hours, minutes] = time.split(":").map(Number);
            return (hours *60) + minutes;
        }

        let totalMinutes =0;
        for(let i=0; i<clockOn.length; i++){
            let clockOnTime = clockOn[i].value;
            let clockOutTime = clockOut[i].value;
            if(clockOnTime && clockOutTime){
                totalMinutes += timeToMinutes(clockOutTime) - timeToMinutes(clockOnTime);
            }
        }

        totalMinutes += timeToMinutes(clockOut1) - timeToMinutes(clockOn1);
        totalMinutes += timeToMinutes(restEnds) - timeToMinutes(restBegins);
        totalMinutes += timeToMinutes(clockOut2) - timeToMinutes(clockOn2);

        let hours = Math.floor(totalMinutes / 60);
        let minutes = totalMinutes % 60;

        dayTime.innerHTML = `${hours}h <span>${minutes}m</span>`;
        
    });


});
