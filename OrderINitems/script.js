document.querySelector(".search i").onclick = function() {
    this.style.display = "none";
    this.parentElement.querySelector("input").style.display = "block";
    this.parentElement.querySelector("input").focus();
};
document.querySelector(".search input").onkeyup = function(event) {
    if (event.keyCode === 13 && this.value.length > 0) {
        window.location.href = encodeURI("index.php?page=search&query=" + this.value);
    }
};
if (document.querySelector(".item-img-small")) {
    let imgs = document.querySelectorAll(".item-img-small");
    imgs.forEach(function(img) {
        img.onclick = function() {
            document.querySelector(".item-img-large").src = this.src;
            imgs.forEach(i => i.classList.remove("selected"));
            this.classList.add("selected");
        };
    });
}
if (document.querySelector(".items-form")) {
    document.querySelector(".sortby select").onchange = () => document.querySelector(".items-form").submit();
    document.querySelector(".category select").onchange = () => document.querySelector(".items-form").submit();
    document.querySelector(".restaurant select").onchange = () => document.querySelector(".items-form").submit();
}
if (document.querySelector(".item #item-form")) {
    document.querySelectorAll(".item #item-form select").forEach(ele => {
        ele.onchange = () => {
            let price = 0.00;
            document.querySelectorAll(".item #item-form select").forEach(e => {
                if (e.value) {
                    price += parseFloat(e.options[e.selectedIndex].dataset.price);
                }
            });
            if (price > 0.00) {
                document.querySelector(".item .price").innerHTML = currency_code + price.toFixed(2);
            }
        };
    });
}
document.querySelector(".responsive-toggle").onclick = function(event) {
    event.preventDefault();
    let nav_display = document.querySelector("header nav").style.display;
    document.querySelector("header nav").style.display = nav_display == "block" ? "none" : "block";
};
