//==== school information fields =====
$('#education-level').on('change', function(e){
    var educationData = $("#educationData");

    if(this.value == 'none')
    {
        educationData.slideUp();
        return;
    }

    educationData.hide();
    educationData.find(".form-group").hide();
    educationData.find(".field_all").show();

    if (this.value == 'vocational_education' || this.value == 'secondary_special') {
        educationData.find(".field_vocational").show();
    } else if(this.value == 'bachelor' || this.value == 'higher') {
        educationData.find(".field_bachelor").show();
    }

    educationData.slideDown();
});

$('.education-kz_holder').on('change', function(e){
    var block = $("#nostrificationBlock");
    console.log(this.value);
    if (this.value == '1') {
        block.slideUp();
    } else {
        block.slideDown();
    }

});