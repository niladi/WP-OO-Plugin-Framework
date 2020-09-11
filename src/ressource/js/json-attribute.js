let $ = jQuery;

$(document).ready(function () {
    $('input.json-attribute-display').each(function () {
        display(this)
    })
})
function display(input) {
    let attributes = JSON.parse($(input).val())
    let div = $(input).next()
    if ($(div).is('div.json-attribute-display')) {
       $(div).empty()
    } else {
        $(input).after('<div class="json-attribute-display"></div>')
        div = $(input).next()
    }

    if (Array.isArray(attributes)) {
        attributes.forEach((element, index) => {
            $(div).append(createRow(() => removeElement(input, index), element))
        })
        $(div).append(createAddButton('Add Element', () => addElement(input)))
    } else if (attributes !== null && typeof(attributes) === 'object') {
        Object.keys(attributes).forEach((key) => {
            $(div).append(createRow(() => removeKey(input, key), `${key} : ${attributes[key]}`))
        })
        $(div).append(createAddButton('Add Element', () => addKeyValue(input)))
    } else {
        console.log('cant map to attribute or object')

    }
}
function getAttributes(input) {
    return JSON.parse($(input).val());
}

function setAttributes(input, attributes) {
    $(input).val(JSON.stringify(attributes))
    display(input)
}

function createAddButton(text, func) {
    let button = document.createElement('button');
    button.type = "button"
    button.className = 'button button-primary button-large '
    button.innerText = text
    button.addEventListener('click', func)

    return button
}

function createRow(func, text) {

    let button = document.createElement('button')
    button.type = "button"
    button.className = 'button button-primary button-small '
    button.innerText = 'del'
    button.style.float = "right";
    button.addEventListener('click', func)

    let row = document.createElement('div')
    row.style.minHeight = "26px"
    row.style.borderBottom = "1px solid black"
    $(row).append(`<span> ${text} </span>`)
    row.append(button)

    return row
}

function addElement(input) {
    let attributes = getAttributes(input)
    result = prompt ("Element: ", '')
    if (result) {
        attributes.push(result)
        setAttributes(input, attributes)
    }
}

function removeElement(input, index) {
    let attributes = getAttributes(input)
    console.log('remove: ' + index)
    attributes.splice(index, 1)
    setAttributes(input, attributes)
}

function addKeyValue(input) {
    let attributes = getAttributes(input)
    key = prompt ("Key: ", '')
    if (key) {
        if (attributes.hasOwnProperty(key)) {
            alert('Key already exists')
            return
        }
        value = prompt ('Value', '')
        if (value) {
            attributes[key] = value
            setAttributes(input, attributes)
        }
    }
}

function removeKey(input, key) {
    let attributes = getAttributes(input)
    delete attributes[key]
    setAttributes(input, attributes)
}
