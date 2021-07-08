function loadFile(event, type) {
    let get_url = URL.createObjectURL(event.target.files[0]);
    let review_image;
    switch (type) {
        case 0:
            review_image = document.querySelector("#review-profile-image");
            break;
        case 1:
            review_image = document.querySelector("#review-cover-image");
            break;
    }
    review_image.src = get_url;
    review_image.onload = function() {
        URL.revokeObjectURL(review_image.src)
    }
    let review_group;
    if (type == 0) {
        review_group = document.querySelector(".review-group-profile");
    } else {
        review_group = document.querySelector(".review-group-cover");
    }
    if (review_group.classList.contains("d-none")) {
        review_group.classList.remove("d-none");
    }
}

function loadFileCreateChat(event) {
    let get_url = URL.createObjectURL(event.target.files[0]);
    let review_image = document.querySelector("#review-image");
    review_image.src = get_url;
    review_image.onload = function() {
        URL.revokeObjectURL(review_image.src)
    }
    let review_group = document.querySelector(".review-group");
    if (review_group.classList.contains("d-none")) {
        review_group.classList.remove("d-none");
    }
}

function loadFileEditChat(event) {
    let get_url = URL.createObjectURL(event.target.files[0]);
    let review_image = document.querySelector("#review-image-edit");
    review_image.src = get_url;
    review_image.onload = function() {
        URL.revokeObjectURL(review_image.src)
    }
}

function loadFileCreatePost(event) {
    let get_url = URL.createObjectURL(event.target.files[0]);
    let review_image = document.querySelector("#review-post-image");
    review_image.src = get_url;
    review_image.onload = function() {
        URL.revokeObjectURL(review_image.src)
    }
    let review_group = document.querySelector(".review-group-post");
    if (review_group.classList.contains("d-none")) {
        review_group.classList.remove("d-none");
    }
}

function loadFileEditPost(event) {
    let get_url = URL.createObjectURL(event.target.files[0]);
    let review_image = document.querySelector("#review-edit-post-image");
    review_image.src = get_url;
    review_image.onload = function() {
        URL.revokeObjectURL(review_image.src)
    }
    let review_group = document.querySelector(".review-group-post-edit");
    if (review_group.classList.contains("d-none")) {
        review_group.classList.remove("d-none");
    }
}