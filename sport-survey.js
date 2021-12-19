//variables
let Ss_exercise_type = document.querySelectorAll(".pa_workout-type");
let Ss_workout_times = document.querySelectorAll(".pa_workout-level");
let Ss_submit_button = document.querySelector("#sports_survey_submit");
let Ss_filter_button = document.querySelector("#sports_survey_filter");
let products = document.querySelectorAll(".product_card");
let Ss_products_display = document.querySelector("#products_display");
let Ss_overlay = document.querySelector(".Ssurvey_overlay");
let Ss_chkbox_addToCart = document.querySelectorAll(".product_to_cart_checkbox");
let Ss_checkable_answers = document.querySelectorAll(".checkable_answer");
let Ss_next_buttons = document.querySelectorAll(".ss_nextform");
let allLegends = document.querySelectorAll("legend");
let allSteps = document.querySelectorAll(".form_class");

//events
Ss_filter_button.addEventListener("click", e => Ssurvey_getProducts(e));
Ss_submit_button.addEventListener("click", e => Ssurvey_addToCart(e));
Ss_chkbox_addToCart.forEach(item => item.addEventListener("change", () => Ssurvey_CssAddToCartChkbox(item)));
Ss_checkable_answers.forEach(item => item.addEventListener("change", () => Ssurvey_CssChkbox(item)));
Ss_next_buttons.forEach(item => item.addEventListener("click", e => Ssurvey_nextStep(item, e)));

//functions
function Ssurvey_getProducts(e) {
  e.preventDefault();
  let exercise_type_checked = [];
  let workout_times_checked = "";

  for (i = 0; i < Ss_exercise_type.length; i++) {
    if (Ss_exercise_type[i].checked) exercise_type_checked[i] = Ss_exercise_type[i].value;
  }

  for (i = 0; i < Ss_workout_times.length; i++) {
    if (Ss_workout_times[i].checked) workout_times_checked = Ss_workout_times[i].value;
  }

  for (i = 0; i < products.length; i++) {
    let inWorkkoutTimes;
    let inWorkkoutLevel;

    product_pa_wl = products[i].getElementsByClassName("workout-level")[0].textContent.split(", ");
    product_pa_wt = products[i].getElementsByClassName("workout-times")[0].textContent.split(", ");
    products[i].parentElement.classList.add("hide");
    products[i].parentElement.classList.remove("checked_add_to_cart");
    products[i].parentElement.getElementsByClassName("product_to_cart_checkbox").checked = false;
    inWorkkoutTimes = false;
    inWorkkoutLevel = false;

    product_pa_wl.map(product_term => {
      if (product_term.toUpperCase() == workout_times_checked.toUpperCase()) inWorkkoutTimes = true;
    });

    product_pa_wt.map(product_term => {
      exercise_type_checked.map(exercise_type_checked_term => {
        if (product_term.toUpperCase() == exercise_type_checked_term.toUpperCase()) inWorkkoutLevel = true;
      });
    });

    if (inWorkkoutTimes && inWorkkoutLevel) {
      products[i].parentElement.classList.toggle("hide");
      products[i].parentElement.classList.toggle("checked_add_to_cart");
      products[i].parentElement.getElementsByClassName("product_to_cart_checkbox")[0].checked = true;
    }
  }
  Ssurvey_CssAddToCartChkbox();
  Ssurvey_nextStep(e.target, e);
}

function Ssurvey_addToCart(e) {
  e.preventDefault();
  let productIDs = [];
  for (i = 0, j = 0; i < products.length; i++) {
    if (!products[i].parentElement.classList.contains("hide") && products[i].parentElement.getElementsByClassName("product_to_cart_checkbox")[0].checked) {
      productIDs[j] = products[i].dataset.id;
      j++;
    }
  }
  overlayOpen(true);
  ajax_addToCart(productIDs, 0);
}

function ajax_addToCart(ids, current) {
  var data = {
    action: "woocommerce_ajax_add_to_cart",
    product_id: ids[current]
  };

  jQuery.ajax({
    type: "post",
    url: wc_add_to_cart_params.ajax_url,
    data: data,
    success: function () {
      current++;
      if (current < ids.length) ajax_addToCart(ids, current);
      else window.location.href = ssData.siteurl + "/checkout";
    }
  });
}

function Ssurvey_CssAddToCartChkbox(item) {
  if (item) item.parentElement.classList.toggle("checked_add_to_cart");
  for (i = 0, j = 0; i < products.length; i++) {
    if (!products[i].parentElement.classList.contains("hide") && products[i].parentElement.getElementsByClassName("product_to_cart_checkbox")[0].checked) {
      j++;
    }
  }
  if (j == 0) {
    Ss_submit_button.textContent = "Select at least one";
    Ss_submit_button.disabled = true;
  } else {
    Ss_submit_button.disabled = false;
    Ss_submit_button.textContent = "Add " + j + " selected to cart";
  }
}

function Ssurvey_CssChkbox(item) {
  item.classList.toggle("checked");
}

function Ssurvey_nextStep(item, e) {
  e.preventDefault();
  if (validateInput(item.parentElement.parentElement)) {
    if (item.parentElement.parentElement.getElementsByClassName("ss_name")[0]) {
      userName = item.parentElement.parentElement.getElementsByClassName("ss_name")[0].value;
      allLegends.forEach(item => {
        item.textContent = item.textContent.replace(/{name}/i, userName);
      });
    }
    nextStep = parseInt(item.parentElement.parentElement.dataset.order) + 1;
    //console.log(nextStep);
    allSteps.forEach(step => {
      step.classList.add("hide");
      if (step.dataset.order == nextStep) step.classList.remove("hide");
    });
  }
}

function overlayOpen(status) {
  if (status) Ss_overlay.classList.remove("disabled");
  else Ss_overlay.classList.add("disabled");
}

function validateInput(input_div) {
  if (input_div.getElementsByClassName("text_field")[0]) {
    let inputField = input_div.getElementsByClassName("text_field")[0];
    if (inputField.value.trim() === "") {
      input_div.getElementsByClassName("error")[0].textContent = "* This field cannot be blank.";
      return false;
    }
    return true;
  }
  if (input_div.getElementsByClassName("checkable_answer")[0]) {
    let inputField = input_div.getElementsByClassName("checkable_answer");
    for (i = 0; i < inputField.length; i++) {
      if (inputField[i].checked) return true;
    }
    input_div.getElementsByClassName("error")[0].textContent = "* Please select at least one.";
    return false;
  }

  if (input_div.getElementsByClassName("radioable_answer")[0]) {
    let inputField = input_div.getElementsByClassName("radioable_answer");
    for (i = 0; i < inputField.length; i++) {
      if (inputField[i].checked) return true;
    }
    input_div.getElementsByClassName("error")[0].textContent = "* Please select one option.";
    return false;
  }

  return true;
}
