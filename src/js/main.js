function resetForm(form) {
// Clearing inputs
    var inputs = form.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        switch (inputs[i].type) {
// Case 'hidden':
            case 'text':
                inputs[i].value = '';
                break;
            case 'radio':
            case 'checkbox':
                inputs[i].checked = false;
            case 'number':
                inputs[i].value = '';

        }
    }

// Clearing selects
    var selects = form.getElementsByTagName('select');
    for (var i = 0; i < selects.length; i++)
        selects[i].selectedIndex = 0;

// Clearing textarea
    var text = form.getElementsByTagName('textarea');
    for (var i = 0; i < text.length; i++)
        text[i].innerHTML = '';

    return false;
}