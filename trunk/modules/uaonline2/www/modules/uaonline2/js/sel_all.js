    $(document).ready( function() {
       $("#example_maincb").click( function() { // при клике по главному чекбоксу
            if($('#example_maincb').attr('checked')){ // проверяем его значение
                $('.example_check:enabled').attr('checked', true); // если чекбокс отмечен, отмечаем все чекбоксы
            } else {
                $('.example_check:enabled').attr('checked', false); // если чекбокс не отмечен, снимаем отметку со всех чекбоксов
            }
       });
    });