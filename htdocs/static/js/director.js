
SC.$(document).ready(function(){

	// Variables
	SC.spacename = SC.$('a#title').text();
	SC.spacename = SC.spacename.replace(/\//, '');


	// Functions
	
	//Highlights
	SC.highlight = function(){

		SC.$('.highlight').animate({ backgroundColor: '#e6732c' }, 500, function(){
			
			SC.$('.highlight').animate({ backgroundColor: '#fff' }, 5000, function(){
				
				SC.$('.highlight').css('background-color', '');

			});
			
		});

		SC.$('.error').animate({ backgroundColor: '#f00' }, 0, function(){
			
			SC.$('.error').animate({ backgroundColor: '#f00' }, 1000, function(){

				SC.$('.error').animate({ backgroundColor: '#fff' }, 0, function(){
				
					SC.$('.error').css('background-color', '');

				});

			});
			
		});

	}


	//Autocomplete
	SC.adata = [];
	SC.$.each(SC.$('#words a'), function(i, e){

		var word = SC.$(e).html()
		word = word.replace(/&amp;/, '&');

		SC.adata.push(word);

	});

	SC.$('#word').autocomplete(SC.adata, {
		autoFill: false
	});

	//Focus
	SC.$('#search').focus();
	SC.$('#spacename').focus();
	SC.$('#word').focus();

	//Highlights
	SC.highlight();

	//Periodic AJAX
	if(SC.$('#search').val()){}else{

		SC.$('#words').everyTime(5000, function(){

			SC.$('#words').load(base_url + 'x/ajax/tagcloud/' + SC.spacename, function(){

				SC.highlight();

			});

			SC.$('#word').attr('class', '');

		});

	}

});

