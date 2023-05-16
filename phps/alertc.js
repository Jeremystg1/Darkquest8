
window.addEventListener("DOMContentLoaded", (event) => {
    //const urlParams = new URLSearchParams(queryString);


    alertc = document.getElementById("_alertc");
    let tempsEnSec = 5;
    console.log("aaa");
    setTimeout(() =>{
        console.log("temps fini");
        alertc.style["font-size"] = 0;
    },tempsEnSec*1000);
});
