/*
Copyright (c) 2018 Sharpay Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


const modelDefaults = {
	type: 'simple',
	language: '',
	height: '32',
	position: 'right',
	style: 'dark',
	shareCounter: false,
	shareCounterMode: 'page',
	image: '',
	syncLoad: false,
	colorChange: false,
	fontColor: '#ffffff',
	bgColor: '#ff9933',
	modal: true,
	size: '24',
	sizeCustom: '22',
	form: 'no',
	colorIcons: false,
	color: '#ff9933',
	colorFontSet: false,
	colorFont: '#ff9933',
	weight: 'normal',
	hover: 'official',
	reward: true,
	align: 'left',
	noLimit: true,
	limit: 3,
};

(function() {

	var $ = jQuery;


	$(function() {
		
		if(document.getElementById('sharpay-select-site')) {
		
			$('h3 input[type=checkbox]').change(function(e) {
				$('.settings[data-for=' + e.target.id + ']').toggle();
			});

			$('body').on('change', '.share-counter', function (e) {
				$(e.target).siblings('.share-counter-mode').toggle();
			})
			.on('change', '.use-custom-markup', function(e) {
				$(e.target).parent().siblings('.group').not('.always-visible').toggle();
			})
			.on('change', '.use-custom-colors', function(e) {
				$(e.target).siblings('.colors').toggle();
			});


			var sharpayAppOrigin = 'https://app.sharpay.io';
			document.getElementById('sharpay-select-site').addEventListener('click', function() {
				var origin = window.location.protocol + "//" + window.location.hostname  + (window.location.port ? ':' + window.location.port : '');
				var url = sharpayAppOrigin + '/webmaster?flow=wp&flow_origin=' + encodeURIComponent(origin);
				window.open(url, null, 'height=810,width=1000,status=yes,toolbar=no,menubar=no,location=no');
			});
			window.addEventListener("message", function(event) {

				if (event.origin !== sharpayAppOrigin) {
					return;
				}
				if( /^[0-9]+$/.test( event.data ) ) {
					$('#sharpay-construct-iframe').height( event.data );
				}
				else if( event.data.length >= 4 && event.data.length <= 7 ) {
					document.getElementById('sharpay-site-code').value = event.data;
				}
				else if( event.data.length > 5 ) {
					var model = JSON.parse( event.data );
					var src = {}; // eslint-disable-line no-useless-escape
					if ( model.language !== modelDefaults.language) {
						src.lang = model.language;
					}
					if( model.size ) {
						if( model.size === 'custom' ) {
							src.height = model.sizeCustom;
						} else {
							src.height = model.size;
						}
					}
					if( model.form ) {
						src.form = model.form;
					}
					if ( model.noLimit && model.limit && /^\d+$/.test( model.limit ) ) {
						src.limit = model.limit;
					}
					if( model.colorIcons && model.color ) {
						src.color = model.color;
					}
					if( model.hover &&  model.hover !== 'official' ) {
						src.hover = model.hover;
					}
					if( model.colorFontSet && model.colorFont ) {
						src.font = model.colorFont;
					}
					if( model.align &&  model.align !== 'left' ) {
						src.align = model.align;
					}

					$('#sharpay-static-code').val( JSON.stringify( src ) );
					$('#sharpay-static-model').val( JSON.stringify( model ) );
				}
			});
			
		}

	});	

})();
