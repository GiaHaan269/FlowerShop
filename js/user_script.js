const userBtn = document.querySelector('#user-btn');
userBtn.addEventListener('click', function() {
    const userBox = document.querySelector('.profile');
    userBox.classList.toggle('active');
})

const toggle = document.querySelector('#menu-btn');
toggle.addEventListener('click', function() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('active');
})

let searchFrom = document.querySelector('.search-form');
let userBox = document.querySelector('.profile'); // Định nghĩa userBox
document.querySelector('#search-btn').onclick = () => {
    searchFrom.classList.toggle('active');
    userBox.classList.remove('active');
}

let slider = document.querySelectorAll('.slider-item');
let index = 0;

function nextSlide() {
    slider[index].classList.remove('active');
    index = (index + 1) % slider.length;
    slider[index].classList.add('active');
}

function prevSlide() {
    slider[index].classList.remove('active');
    index = (index - 1 + slider.length) % slider.length;
    slider[index].classList.add('active');
}

// Xử lý hiển thị/ẩn mật khẩu
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
const confirmPassword = document.querySelector('#confirm_password');

// Xử lý sự kiện click cho mật khẩu
togglePassword.addEventListener('click', function() {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('bx-show');
    this.classList.toggle('bx-hide');
});

// Xử lý sự kiện click cho xác nhận mật khẩu
toggleConfirmPassword.addEventListener('click', function() {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    this.classList.toggle('bx-show');
    this.classList.toggle('bx-hide');
});

document.addEventListener('DOMContentLoaded', function() {
    const shippingSelect = document.querySelector('select[name="shipping"]');
    const shippingFeeContainer = document.querySelector('.shipping-fee');
    const shippingFeeValue = document.getElementById('shipping-fee-value');
    const grandTotalElement = document.querySelector('.grand-total span');
    let initialGrandTotal = parseFloat(grandTotalElement.innerText.replace('tổng thanh toán : ', '').replace('₫', '').trim());

    // Update the total when the shipping method is selected
    shippingSelect.addEventListener('change', function() {
        let shippingFee = 0;
        if (shippingSelect.value === "Vận chuyển tiêu chuẩn") {
            shippingFee = 30000; // Standard shipping fee
        } else if (shippingSelect.value === "Vận chuyển nhanh") {
            shippingFee = 50000; // Express shipping fee
        }

        // Show the shipping fee container
        shippingFeeContainer.style.display = "block";

        // Update the shipping fee value in the UI
        shippingFeeValue.innerText = shippingFee;

        // Update the grand total
        const updatedGrandTotal = initialGrandTotal + shippingFee;
        grandTotalElement.innerText = 'tổng thanh toán : ' + updatedGrandTotal.toLocaleString() + ' VNĐ';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const value = this.getAttribute('data-value');
            resetStars();
            highlightStars(value);
        });

        star.addEventListener('mouseout', function() {
            const selectedValue = ratingInput.value;
            resetStars();
            if (selectedValue) {
                highlightStars(selectedValue);
            }
        });

        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value; // Lưu giá trị đã chọn vào input ẩn
            resetStars();
            highlightStars(value);
        });
    });

    function resetStars() {
        stars.forEach(star => {
            star.classList.remove('selected');
        });
    }

    function highlightStars(value) {
        for (let i = 0; i < value; i++) {
            stars[i].classList.add('selected');
        }
    }
});
// (function() {
//     const giay = 1000,
//         phut = giay * 60,
//         gio = phut * 60,
//         ngay = gio * 24;

//     let today = new Date(),
//         dd = String(today.getDate()).padStart(2, "0"),
//         mm = String(today.getMonth() + 1).padStart(2, "0"),
//         yyyy = today.getFullYear(),
//         nextYear = yyyy + 1,
//         dayMonth = "09/30/",
//         birthday = dayMonth + yyyy;
//     today = mm + "/" + dd + "/" + yyyy;
//     if (today > birthday) {
//         birthday = dayMonth + nextYear;
//     }
//     const countDown = new Date(birthday).getTime(),
//         x = setInterval(function() {
//             const now = new Date().getTime(),
//                 distance = countDown - now;
//             document.getElementById("days").innerText = Math.floor(distance / (ngay));
//             document.getElementById("hours").innerText = Math.floor((distance % (ngay)) / (gio));
//             document.getElementById("minutes").innerText = Math.floor((distance % (gio)) / (phut));
//             document.getElementById("seconds").innerText = Math.floor((distance % (phut)) / (giay));
//         }, 0)
// }());