import {
    Sidenav,
    initTE,
    Carousel,
    Datepicker,
    Select,
    Timepicker,
    Dropdown,
    Ripple,
    Toast,
    Tooltip,
    Popover,
    Input,
    Alert,
    Modal,

} from "tw-elements";
import axios, {isCancel, AxiosError} from 'axios';


window.axios = axios.create();
window.axios.interceptors.response.use(undefined, function (error) {
        error.handleGlobally = (error) => {
            return () => {
                const statusCode = error.response ? error.response.status : null;
                if (statusCode === 419) {
                    location.reload();
                }

            }
        }

        return Promise.reject(error);
    }
);
window.onload = (event) => {

    // window.tailwindElements();
    try {
        if (
            localStorage.theme === "dark" ||
            (!("theme" in localStorage) &&
                window.matchMedia("(prefers-color-scheme: dark)").matches)
        ) {
            document.documentElement.classList.add("dark");
            document
                .querySelector('meta[name="theme-color"]')
                .setAttribute("content", "#0B1120");
        } else {
            document.documentElement.classList.remove("dark");
        }
    } catch (_) {
    }
    // initChat();

}

window.tailwindElements = () => {

    // if (!window.Select) {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-te-toggle="tooltip"]'));
    tooltipTriggerList.map((tooltipTriggerEl) => new Tooltip(tooltipTriggerEl));
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-te-toggle="popover"]'));
    popoverTriggerList.map((popoverTriggerList) => new Popover(popoverTriggerList));
    // console.log(Dropdown);
    //
    const dropdownElementList = [].slice.call(document.querySelectorAll('[data-te-dropdown-toggle-ref]'));
    window.dropdownList = dropdownElementList.map((dropdownToggleEl) => {
        let d = new Dropdown(dropdownToggleEl);
        dropdownToggleEl.addEventListener('click', function (event) {
            d.toggle();
        })
        return d;
    });

    // window.Popover = Popover;
    // window.Tooltip = Tooltip;
    // window.Select = Select;
    // window.Alert = Alert;
    // window.Toast = Toast;
    // window.Sidenav = Sidenav;
    // window.Modal = Modal;
    // window.Dropdown = Dropdown;
    initTE({Popover, Tooltip, Ripple, Input, Select, Alert, Toast, Sidenav, Modal,}, {
        // allowReinits: true,
        autoReinits: true,
        checkOtherImports: false,
    });
    // if (!window.Dropdown) {
    //     //
    //     window.Dropdown = Dropdown;
    //     initTE({Dropdown}, {
    //         allowReinits: true,
    //         autoReinits: true
    //     });
    //
    // }
    document.querySelectorAll("[data-te-input-notch-ref]").forEach(el => el.setAttribute("dir", "ltr"))
    document.querySelectorAll("[data-te-input-notch-ref]").forEach(el => el.innerHTML = '')

    const alertEl = document.getElementById('alert');
    const toastEl = document.getElementById('toast');
    const modalEl = document.getElementById('modal');
    const sideNavEl = document.getElementById('sidenav-1');
    if (alertEl)
        window.Alert = new Alert(alertEl);
    // window.Alert = Alert.getInstance(alertEl);
    if (toastEl)
        window.Toast = new Toast(toastEl);
    // window.Toast = Toast.getInstance(toastEl);
    if (modalEl)
        window.Modal = new Modal(modalEl);
    if (sideNavEl)
        // if (!window.Sidenav) {
        window.Sidenav = new Sidenav(sideNavEl);
    // } else
    //     window.Sidenav = Sidenav.getInstance(sideNavEl);

    initSidenav();
    // }
}

window.initSidenav = () => {


    let innerWidth = null;

    const setMode = (e) => {
        // Check necessary for Android devices
        if (window.innerWidth === innerWidth) {
            return;
        }

        innerWidth = window.innerWidth;
        // console.log(window.Sidenav);
        if (!window.Sidenav) return;

        if (window.innerWidth < window.Sidenav.getBreakpoint("md")) {
            window.Sidenav.changeMode("over");
            // console.log('hide');
            window.Sidenav.hide();
            // window.Sidenav.show();
        } else {
            window.Sidenav.changeMode("side");
            // console.log('show');
            window.Sidenav.show();
        }
    };

    if (window.Sidenav && window.innerWidth < window.Sidenav.getBreakpoint("md")) {
        setMode();
    }

    window.addEventListener("resize", setMode);
}
window.f2e = function (str) {
    if (!str) return str;
    str = str.toString();
    let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    for (let i in per) {
//                    str = str.replaceAll(eng[i], per[i]);
        let re = new RegExp(per[i], "g");
        str = str.replace(re, eng[i]);
    }

    return str;


};
window.e2f = function (str) {
    let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    if (!str) return str;
    if (Array.isArray(str)) {
        for (let idx in str) {
            for (let i in per) {
//                    str = str.replaceAll(eng[i], per[i]);
                let re = new RegExp(eng[i], "g");
                str[idx] = str[idx].replace(re, per[i]);
            }
        }
        return str;
    }
    str = str.toString();

    for (let i in per) {
//                    str = str.replaceAll(eng[i], per[i]);
        let re = new RegExp(eng[i], "g");
        str = str.replace(re, per[i]);
    }
    return str;


};

window.initChat = function () {
    var i = "5EEMY6", a = window, d = document;

    function g() {
        var g = d.createElement("script"), s = "https://www.goftino.com/widget/" + i,
            l = localStorage.getItem("goftino_" + i);
        g.async = !0, g.src = l ? s + "?o=" + l : s;
        d.getElementsByTagName("head")[0].appendChild(g);
    }

    "complete" === d.readyState ? g() : a.attachEvent ? a.attachEvent("onload", g) : a.addEventListener("load", g, !1);

}