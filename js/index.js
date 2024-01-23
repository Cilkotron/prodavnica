console.log('Stranica je ucitana!');

// nakon 5s sakrije alert
setTimeout(function() {
  $('.alert').slideUp(/* 'slow' | 'fast' | 1000 */);
}, 5000);

$('.show-popup').click(function() {
  $('.popup-background').fadeIn().css('display', 'flex');
});

$('.hide-popup').click(function() {
  $('.popup-background').slideUp();
});




// // kreiranje promenljive
// var mojH1 = "Nesto...";
// $mojH1 = "Nesto...";


// // promena verednosti
// $mojH1 = "nova vrednost...";
// mojH1 = "nova vrednost";