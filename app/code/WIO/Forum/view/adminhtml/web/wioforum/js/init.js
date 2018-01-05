//alert(' window.onload ');
window.onload = function() {
  tinymce.init({
    selector: "textarea#forum_post",
    height: 400,

    plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
      "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
      "table contextmenu directionality emoticons textcolor paste textcolor colorpicker textpattern"
    ],
    relative_urls: false,
    remove_script_host: false,
    toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
    toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking pagebreak restoredraft",
    menubar: false,
    toolbar_items_size: 'small',

  });
}

function ___triggerTinyMce(form) {
  setTimeout(function(){
    document.getElementById('forum_post').value = tinyMCE.activeEditor.getContent({ format: 'raw' });
    form.submit();
  }, 150)
  return false;
}