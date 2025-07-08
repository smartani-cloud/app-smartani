const level_tk = [{
        "id": 1,
        "level": "TK A",
        "level_romawi": "A",
        "level_char": "a",
        "created_at": null,
        "updated_at": null,
        "unit_id": 1
    },
    {
        "id": 2,
        "level": "TK B",
        "level_romawi": "B",
        "level_char": "b",
        "created_at": null,
        "updated_at": null,
        "unit_id": 1
    },
    {
        "id": 15,
        "level": "KB",
        "level_romawi": "KB",
        "level_char": "kb",
        "created_at": null,
        "updated_at": null,
        "unit_id": 1
    }
];

const level_sd = [{
    "id": 3,
    "level": "1",
    "level_romawi": "I",
    "level_char": "satu",
    "created_at": null,
    "updated_at": null,
    "unit_id": 2
}, {
    "id": 4,
    "level": "2",
    "level_romawi": "II",
    "level_char": "dua",
    "created_at": null,
    "updated_at": null,
    "unit_id": 2
}, {
    "id": 5,
    "level": "3",
    "level_romawi": "III",
    "level_char": "tiga",
    "created_at": null,
    "updated_at": null,
    "unit_id": 2
}, {
    "id": 6,
    "level": "4",
    "level_romawi": "IV",
    "level_char": "empat",
    "created_at": null,
    "updated_at": null,
    "unit_id": 2
}, {
    "id": 7,
    "level": "5",
    "level_romawi": "V",
    "level_char": "lima",
    "created_at": null,
    "updated_at": null,
    "unit_id": 2
}, {
    "id": 8,
    "level": "6",
    "level_romawi": "VI",
    "level_char": "enam",
    "created_at": null,
    "updated_at": null,
    "unit_id": 2
}];

const level_smp = [{
    "id": 9,
    "level": "7",
    "level_romawi": "VII",
    "level_char": "tujuh",
    "created_at": null,
    "updated_at": null,
    "unit_id": 3
}, {
    "id": 10,
    "level": "8",
    "level_romawi": "VIII",
    "level_char": "delapan",
    "created_at": null,
    "updated_at": null,
    "unit_id": 3
}, {
    "id": 11,
    "level": "9",
    "level_romawi": "IX",
    "level_char": "sembilan",
    "created_at": null,
    "updated_at": null,
    "unit_id": 3
}];

const level_sma = [{
    "id": 12,
    "level": "10",
    "level_romawi": "X",
    "level_char": "sepuluh",
    "created_at": null,
    "updated_at": null,
    "unit_id": 4
}, {
    "id": 13,
    "level": "11",
    "level_romawi": "XI",
    "level_char": "sebelas",
    "created_at": null,
    "updated_at": null,
    "unit_id": 4
}, {
    "id": 14,
    "level": "12",
    "level_romawi": "XII",
    "level_char": "dua belas",
    "created_at": null,
    "updated_at": null,
    "unit_id": 4
}];


function changeUnit(unit_id) {
    var levels = [];
    console.log(unit_id);
    switch (unit_id) {
        case '1':
            createOption(level_tk);
            break;
        case '2':
            createOption(level_sd);
            break;
        case '3':
            createOption(level_smp);
            break;
        case '4':
            createOption(level_sma);
            break;
        default:
            createOption([]);
    }
}

function createOption(list) {
    $('option[class="level_option"]').remove();
    list.map((item, index) => {
        const option = '<option value="' + item.id + '" class="level_option">' + item.level + '</option>';
        $('select[name="level"]').append(option);
    });
}
