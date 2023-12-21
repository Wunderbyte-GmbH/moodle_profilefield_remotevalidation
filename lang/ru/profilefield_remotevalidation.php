<?php

/**
 * Contains language strings
 *
 * @package    profilefield_remotevalidation
 * @category   profilefield
 * @copyright  2023 Georg Maißer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//name of the plugin should be defined.
$string['pluginname'] = 'Удаленная валидация';
$string['privacy:metadata'] = 'Тип профиля "Удаленная валидация" представляет собой всего лишь расширение стандартных полей профиля пользователя. Он не передает данные на внешнюю платформу.';

$string['noserverdefined'] = 'Не определен сервер для удаленной валидации. Сообщите вашему администратору.';
$string['problemwithserver'] = 'Неверный ответ от вашего определенного сервера.';
$string['yourpinisinvalid'] = 'Ваши данные для {$a} кажутся недействительными. Пожалуйста, попробуйте снова.';
$string['regexpattern'] = 'Введите шаблон регулярного выражения (RegEx) для проведения первичной валидации PIN. Это делается перед удаленной проверкой.';
$string['regexpattern_help'] = 'Введите шаблон регулярного выражения (RegEx) здесь, как если бы вы делали это на https://regex101.com/. Пример для проверки числа
от 9 до 12 цифр: ^[0-9]{9,12}';
$string['remoteservice'] = 'URL удаленного веб-сервиса.';
$string['remoteservice_help'] = 'Значение пользовательского ввода, которое вы хотите проверить, представлено заполнителем $$:
 https://example.com/api/GetInfo?fieldvalue=$$&token=1234567890';
$string['wrongpattern'] = 'Ваш ввод не соответствует требуемому шаблону.';
$string['validationerror'] = 'При валидации возникла следующая ошибка: ';
$string['valuealreadyset'] = 'Это значение уже установлено для другого пользователя. Пожалуйста, перейдите по ссылке {$a}, чтобы восстановить свой пароль.';

