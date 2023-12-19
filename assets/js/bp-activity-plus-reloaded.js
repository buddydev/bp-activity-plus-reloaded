var _bpfbActiveHandler = false;

( function( $ ) {
	$( function() {
		var $form, $text, $textContainer;

		/**
		 * Video insertion/preview handler.
		 */
		var BpfbVideoHandler = function() {
			var $container = $( '.bpfb_controls_container' );

			function resize() {
				$( '#bpfb_video_url' ).width( $container.width() );
			}

			function createVideoMarkupFunc() {
				var html = '<input type="text" id="bpfb_video_url" name="bpfb_video_url" placeholder="' + BPAPRJSData.paste_video_url + '" value="" />' +
								'<input type="button" id="bpfb_video_url_preview" value="' + BPAPRJSData.preview + '" />';
				$container.empty().append( html );

				$( window ).off( 'resize.bpfb' ).on( 'resize.bpfb', resize );
				resize();

				$( '#bpfb_video_url' ).focus( function() {
					$( this )
						.select()
						.addClass( 'changed' )
					;
				} );

				$( '#bpfb_video_url' ).keypress( function( e ) {
					if ( 13 !== e.which ) {
						return true;
					}
					createVideoPreview();
					return false;
				} );
				$( '#bpfb_video_url' ).change( createVideoPreview );
				$( '#bpfb_video_url_preview' ).click( createVideoPreview );
			}

			function createVideoPreview() {
				var url = $( '#bpfb_video_url' ).val();
				if ( ! url ) {
					return false;
				}
				$( '.bpfb_preview_container' ).html( '<div class="bpfb_waiting"></div>' );
				$.post( ajaxurl, { action: 'bpfb_preview_video', data: url }, function( data ) {
					$( '.bpfb_preview_container' ).empty().html( data );
					$( '.bpfb_action_container' ).html(
						'<p><input type="button" class="button-primary bpfb_primary_button" id="bpfb_submit" value="' + BPAPRJSData.add_video + '" /> ' +
							'<input type="button" class="button" id="bpfb_cancel" value="' + BPAPRJSData.cancel + '" /></p>'
					);
					$( '#bpfb_cancel_action' ).hide();
				} );
			}

			function processForSave() {
				return {
					bpfb_video_url: $( '#bpfb_video_url' ).val()
				};
			}

			function init() {
				$( '#aw-whats-new-submit' ).hide();
				createVideoMarkupFunc();
			}

			function destroy() {
				$container.empty();
				$( '.bpfb_preview_container' ).empty();
				$( '.bpfb_action_container' ).empty();
				$( '#aw-whats-new-submit' ).show();
				$( window ).off( 'resize.bpfb' );
			}

			init();

			return { destroy: destroy, get: processForSave };
		};

		/**
		 * Link insertion/preview handler.
		 */
		var BpfbLinkHandler = function() {
			var $container = $( '.bpfb_controls_container' );

			function resize() {
				$( '#bpfb_link_preview_url' ).width( $container.width() );
			}

			function createMarkup() {
				var html = '<input type="text" id="bpfb_link_preview_url" name="bpfb_link_preview_url" placeholder="' + BPAPRJSData.paste_link_url + '" value="" />' +
							'<input type="button" id="bpfb_link_url_preview" value="' + BPAPRJSData.preview + '" />';
				$container.empty().append( html );

				$( window ).off( 'resize.bpfb' ).on( 'resize.bpfb', resize );
				resize();
				$( '#bpfb_link_preview_url' ).focus( function() {
					$( this )
						.select()
						.addClass( 'changed' )
					;
				} );

				$( '#bpfb_link_preview_url' ).keypress( function( e ) {
					if ( 13 !== e.which ) {
						return true;
					}
					createLinkPreview();
					return false;
				} );
				$( '#bpfb_link_preview_url' ).change( createLinkPreview );
				$( '#bpfb_link_url_preview' ).click( createLinkPreview );
			}

			function createPreviewMarkup( data ) {
				var imgs = '',
					url, html, $img;
				if ( ! data.url ) {
					$( '.bpfb_preview_container' ).empty().html( data.title );
					return false;
				}

				$.each( data.images, function( idx, img ) {
					if ( ! img ) {
						return true;
					}
					url = img.match( /^http/ ) ? img : data.url + '/' + img;
					imgs += '<img class="bpfb_link_preview_image" src="' + url + '" />';
				} );
				html = '<table border="0">' +
			'<tr>' +
				'<td>' +
					'<div class="bpfb_link_preview_container">' +
						imgs +
						'<input type="hidden" name="bpfb_link_img" value="" />' +
					'</div>' +
				'</td>' +
				'<td>' +
					'<div class="bpfb_link_preview_title">' + data.title + '</div>' +
					'<input type="hidden" name="bpfb_link_title" value="' + data.title + '" />' +
					'<div class="bpfb_link_preview_url">' + data.url + '</div>' +
					'<input type="hidden" name="bpfb_link_url" value="' + data.url + '" />' +
					'<div class="bpfb_link_preview_body">' + data.text + '</div>' +
					'<input type="hidden" name="bpfb_link_body" value="' + data.text + '" />' +
					'<div class="bpfb_thumbnail_chooser">' +
						'<span class="bpfb_left"><img class="bpfb_thumbnail_chooser_left" src="' + BPAPRConfig.root_url + '/assets/img/system/left.gif" />&nbsp;</span>' +
						'<span class="bpfb_thumbnail_chooser_label">' + BPAPRJSData.choose_thumbnail + '</span>' +
						'<span class="bpfb_right">&nbsp;<img class="bpfb_thumbnail_chooser_right" src="' + BPAPRConfig.root_url + '/assets/img/system/right.gif" /></span>' +
						'<br /><input type="checkbox" id="bpfb_link_no_thumbnail" /> <label for="bpfb_link_no_thumbnail">' + BPAPRJSData.no_thumbnail + '</label>' +
					'</div>' +
				'</td>' +
			'</tr>' +
		'</table>';
				$( '.bpfb_preview_container' ).empty().html( html );
				$( '.bpfb_action_container' ).html(
					'<p><input type="button" class="button-primary bpfb_primary_button" id="bpfb_submit" value="' + BPAPRJSData.add_link + '" /> ' +
			'<input type="button" class="button" id="bpfb_cancel" value="' + BPAPRJSData.cancel + '" /></p>'
				);
				$( '#bpfb_cancel_action' ).hide();

				$( 'img.bpfb_link_preview_image' ).hide();
				$( 'img.bpfb_link_preview_image' ).first().show();
				$( 'input[name="bpfb_link_img"]' ).val( $( 'img.bpfb_link_preview_image' ).first().attr( 'src' ) );

				//$('.bpfb_thumbnail_chooser_left').click(function () {
				$( '.bpfb_thumbnail_chooser .bpfb_left' ).click( function() {
					var $cur = $( 'img.bpfb_link_preview_image:visible' );
					var $prev = $cur.prev( '.bpfb_link_preview_image' );
					if ( $prev.length ) {
						$cur.hide();
						$prev
							.width( $( '.bpfb_link_preview_container' ).width() )
							.show();
						$( 'input[name="bpfb_link_img"]' ).val( $prev.attr( 'src' ) );
					}
					return false;
				} );
				//$('.bpfb_thumbnail_chooser_right').click(function () {
				$( '.bpfb_thumbnail_chooser .bpfb_right' ).click( function() {
					var $cur = $( 'img.bpfb_link_preview_image:visible' );
					var $next = $cur.next( '.bpfb_link_preview_image' );
					if ( $next.length ) {
						$cur.hide();
						$next
							.width( $( '.bpfb_link_preview_container' ).width() )
							.show();
						$( 'input[name="bpfb_link_img"]' ).val( $next.attr( 'src' ) );
					}
					return false;
				} );
				$( '#bpfb_link_no_thumbnail' ).click( function() {
					if ( $( '#bpfb_link_no_thumbnail' ).is( ':checked' ) ) {
						$( 'img.bpfb_link_preview_image:visible' ).hide();
						$( 'input[name="bpfb_link_img"]' ).val( '' );
						$( '.bpfb_left, .bpfb_right, .bpfb_thumbnail_chooser_label' ).hide();
					} else {
						$img = $( 'img.bpfb_link_preview_image:first' );
						$img.show();
						$( '.bpfb_left, .bpfb_right, .bpfb_thumbnail_chooser_label' ).show();
						$( 'input[name="bpfb_link_img"]' ).val( $img.attr( 'src' ) );
					}
				} );
			}

			function createLinkPreview() {
				var url = $( '#bpfb_link_preview_url' ).val();
				if ( ! url ) {
					return false;
				}
				$( '.bpfb_preview_container' ).html( '<div class="bpfb_waiting"></div>' );
				$.post( ajaxurl, { action: 'bpfb_preview_link', data: url }, function( data ) {
					createPreviewMarkup( data );
				} );
			}

			function processForSave() {
				return {
					bpfb_link_url: $( 'input[name="bpfb_link_url"]' ).val(),
					bpfb_link_image: $( 'input[name="bpfb_link_img"]' ).val(),
					bpfb_link_title: $( 'input[name="bpfb_link_title"]' ).val(),
					bpfb_link_body: $( 'input[name="bpfb_link_body"]' ).val()
				};
			}

			function init() {
				$( '#aw-whats-new-submit' ).hide();
				createMarkup();
			}

			function destroy() {
				$container.empty();
				$( '.bpfb_preview_container' ).empty();
				$( '.bpfb_action_container' ).empty();
				$( '#aw-whats-new-submit' ).show();
				$( window ).off( 'resize.bpfb' );
			}

			init();

			return { destroy: destroy, get: processForSave };
		};

		/**
		 * Photos insertion/preview handler.
		 */
		var BpfbPhotoHandler = function() {
			var $container = $( '.bpfb_controls_container' );

			function createMarkup() {
				var uploader, html;
				html = '<div id="bpfb_tmp_photo"> </div>' +
			'<ul id="bpfb_tmp_photo_list"></ul>' +
			'<input type="button" id="bpfb_add_remote_image" value="' + BPAPRJSData.add_remote_image + '" /><div id="bpfb_remote_image_container"></div>' +
			'<input type="button" id="bpfb_remote_image_preview" value="' + BPAPRJSData.preview + '" />';
				$container.append( html );

				uploader = new qq.FileUploader( {
					element: $( '#bpfb_tmp_photo' )[0],
					listElement: $( '#bpfb_tmp_photo_list' )[0],
					allowedExtensions: [ 'jpg', 'jpeg', 'png', 'gif' ],
					action: ajaxurl,
					params: {
						action: 'bpfb_preview_photo'
					},
					onSubmit: function( id ) {
						if ( ! parseInt( BPAPRJSData._max_images, 10 ) ) {
							return true;
						} // Skip check
						id = parseInt( id, 10 );
						if ( ! id ) {
							id = $( 'img.bpfb_preview_photo_item' ).length;
						}
						if ( ! id ) {
							return true;
						}
						if ( id < parseInt( BPAPRJSData._max_images, 10 ) ) {
							return true;
						}
						if ( ! $( '#bpfb-too_many_photos' ).length ) {
							$( '#bpfb_tmp_photo' ).append(
								'<p id="bpfb-too_many_photos">' + BPAPRJSData.images_limit_exceeded + '</p>'
							);
						}
						return false;
					},
					onComplete: createPhotoPreview,
					template: '<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span>' + BPAPRJSData.drop_files + '</span></div>' +
                '<div class="qq-upload-button">' + BPAPRJSData.upload_file + '</div>' +
                '<ul class="qq-upload-list"></ul>' +
             '</div>'
				} );

				$( '#bpfb_remote_image_preview' ).hide();
				$( '#bpfb_tmp_photo' ).click( function() {
					if ( $( '#bpfb_add_remote_image' ).is( ':visible' ) ) {
						$( '#bpfb_add_remote_image' ).hide();
					}
				} );
				$( '#bpfb_add_remote_image' ).click( function() {
					if ( ! $( '#bpfb_remote_image_preview' ).is( ':visible' ) ) {
						$( '#bpfb_remote_image_preview' ).show();
					}
					if ( $( '#bpfb_tmp_photo' ).is( ':visible' ) ) {
						$( '#bpfb_tmp_photo' ).hide();
					}

					if ( $( '.bpfb_remote_image' ).length < parseInt( BPAPRJSData._max_images, 10 ) ) {
						$( '#bpfb_add_remote_image' ).val( BPAPRJSData.add_another_remote_image );
					} else {
						$( '#bpfb_add_remote_image' ).hide();
					}

					$( '#bpfb_remote_image_container' ).append(
						'<input type="text" class="bpfb_remote_image" size="64" value="" /><br />'
					);
					$( '#bpfb_remote_image_container .bpfb_remote_image' ).width( $container.width() );
				} );
				$( document ).on( 'change', '#bpfb_remote_image_container .bpfb_remote_image', createRemoteImagePreview );
				$( '#bpfb_remote_image_preview' ).click( createRemoteImagePreview );
			}

			function createRemoteImagePreview() {
				var imgs = [];
				$( '#bpfb_remote_image_container .bpfb_remote_image' ).each( function() {
					imgs[imgs.length] = $( this ).val();
				} );
				$.post( ajaxurl, { action: 'bpfb_preview_remote_image', data: imgs }, function( data ) {
					var html = '';
					$.each( data, function() {
						html += '<img class="bpfb_preview_photo_item" src="' + this + '" width="80px" />' +
				'<input type="hidden" class="bpfb_photos_to_add" name="bpfb_photos[]" value="' + this + '" />';
					} );
					$( '.bpfb_preview_container' ).html( html );
				} );
				$( '.bpfb_action_container' ).html(
					'<p><input type="button" class="button-primary bpfb_primary_button" id="bpfb_submit" value="' + BPAPRJSData.add_photos + '" /> ' +
			'<input type="button" class="button" id="bpfb_cancel" value="' + BPAPRJSData.cancel + '" /></p>'
				);
				$( '#bpfb_cancel_action' ).hide();
			}

			function createPhotoPreview( id, fileName, resp ) {
				var html;
				if ( 'error' in resp ) {
					return false;
				}
				html = '<img class="bpfb_preview_photo_item" src="' + BPAPRConfig.temp_img_url + resp.file + '" width="80px" />' +
						'<input type="hidden" class="bpfb_photos_to_add" name="bpfb_photos[]" value="' + resp.file + '" />';
				$( '.bpfb_preview_container' ).append( html );
				$( '.bpfb_action_container' ).html(
					'<p><input type="button" class="button-primary bpfb_primary_button" id="bpfb_submit" value="' + BPAPRJSData.add_photos + '" /> ' +
					'<input type="button" class="button" id="bpfb_cancel" value="' + BPAPRJSData.cancel + '" /></p>'
				);
				$( '#bpfb_cancel_action' ).hide();
			}

			function removeTempImages( rtiCallback ) {
				var $imgs = $( 'input.bpfb_photos_to_add' );
				if ( ! $imgs.length ) {
					return rtiCallback();
				}
				$.post( ajaxurl, { action: 'bpfb_remove_temp_images', data: $imgs.serialize().replace( /%5B%5D/g, '[]' ) }, function( data ) {
					rtiCallback();
				} );
			}

			function processForSave() {
				var $imgs = $( 'input.bpfb_photos_to_add' );
				var imgArr = [];
				$imgs.each( function() {
					imgArr[imgArr.length] = $( this ).val();
				} );
				return {
					bpfb_photos: imgArr//$imgs.serialize().replace(/%5B%5D/g, '[]')
				};
			}

			function init() {
				$container.empty();
				$( '.bpfb_preview_container' ).empty();
				$( '.bpfb_action_container' ).empty();
				$( '#aw-whats-new-submit' ).hide();
				createMarkup();
			}

			function destroy() {
				removeTempImages( function() {
					$container.empty();
					$( '.bpfb_preview_container' ).empty();
					$( '.bpfb_action_container' ).empty();
					$( '#aw-whats-new-submit' ).show();
				} );
			}

			removeTempImages( init );

			return { destroy: destroy, get: processForSave };
		};

		/* === End handlers  === */

		/**
		 * Main interface markup creation.
		 */
		function createLayoutMarkup() {

			if ( BPAPRConfig.allowed_items && ! BPAPRConfig.allowed_items.length ) {
				return;
			}

			var photosItem = '';
			var videosItem = '';
			var linkItems = '';

			if ( $.inArray( 'photos', BPAPRConfig.allowed_items ) !== -1 ) {
				photosItem = '<a href="#photos" class="bpfb_toolbarItem" title="' + BPAPRJSData.add_photos_tip + '" id="bpfb_addPhotos"><span>' + BPAPRJSData.add_photos + '</span></a>&nbsp;';
			}

			if ( $.inArray( 'videos', BPAPRConfig.allowed_items ) !== -1 ) {
				videosItem = '<a href="#videos" class="bpfb_toolbarItem" title="' + BPAPRJSData.add_videos + '" id="bpfb_addVideos"><span>' + BPAPRJSData.add_videos + '</span></a>&nbsp;';
			}

			if ( $.inArray( 'links', BPAPRConfig.allowed_items ) !== -1 ) {
				linkItems = '<a href="#links" class="bpfb_toolbarItem" title="' + BPAPRJSData.add_links + '" id="bpfb_addLinks"><span>' + BPAPRJSData.add_links + '</span></a>';
			}

			var html = '<div class="bpfb_actions_container bpfb-theme-' + BPAPRConfig.theme.replace( /[^-_a-z0-9]/ig, '' ) + ' bpfb-alignment-' + BPAPRConfig.alignment.replace( /[^-_a-z0-9]/ig, '' ) + '">' +
		'<div class="bpfb_toolbar_container">'
				+ photosItem +  videosItem + linkItems +
		'</div>' +
		'<div class="bpfb_controls_container">' +
		'</div>' +
		'<div class="bpfb_preview_container">' +
		'</div>' +
		'<div class="bpfb_action_container">' +
		'</div>' +
		'<input type="button" id="bpfb_cancel_action" value="' + BPAPRJSData.cancel + '" style="display:none" />' +
	'</div>';
			$form.wrap( '<div class="bpfb_form_container" />' );
			$textContainer.after( html );
		}

		/**
		 * Initializes the main interface.
		 */
		function setup() {
			$form = $( '#whats-new-form' );
			$text = $form.find( '#whats-new-textarea [name="whats-new"]' );
			$textContainer = $form.find( '#whats-new-textarea' );
			createLayoutMarkup();
			$( '#bpfb_addPhotos' ).click( function() {
				if ( _bpfbActiveHandler ) {
					_bpfbActiveHandler.destroy();
				}
				_bpfbActiveHandler = new BpfbPhotoHandler();
				$( '#bpfb_cancel_action' ).show();
				return false;
			} );
			$( '#bpfb_addLinks' ).click( function() {
				if ( _bpfbActiveHandler ) {
					_bpfbActiveHandler.destroy();
				}
				_bpfbActiveHandler = new BpfbLinkHandler();
				$( '#bpfb_cancel_action' ).show();
				return false;
			} );
			$( '#bpfb_addVideos' ).click( function() {
				if ( _bpfbActiveHandler ) {
					_bpfbActiveHandler.destroy();
				}
				_bpfbActiveHandler = new BpfbVideoHandler();
				$( '#bpfb_cancel_action' ).show();
				return false;
			} );
			$( '#bpfb_cancel_action' ).click( function() {
				$( '.bpfb_toolbarItem.bpfb_active' ).removeClass( 'bpfb_active' );
				_bpfbActiveHandler.destroy();
				$( '#bpfb_cancel_action' ).hide();
				return false;
			} );
			$( '.bpfb_toolbarItem' ).click( function() {
				$( '.bpfb_toolbarItem.bpfb_active' ).removeClass( 'bpfb_active' );
				$( this ).addClass( 'bpfb_active' );
			} );
			$( document ).on( 'click', '#bpfb_submit', function() {
				var params, groupID, content, $postBox;
				$postBox = $( '#whats-new-textarea #whats-new' ).first();
				var $stream = $( '#activity-stream' ),
					$list = $stream.find( '.activity-list' );
				var $this = $(this);

				params = _bpfbActiveHandler.get();
				if ( $postBox.is( 'textarea' ) ) {
					content = $postBox.val();
				} else if ( $postBox.is( 'div' ) ) {
					$postBox.find( 'img.emojioneemoji' ).replaceWith( function() {
						return this.dataset.emojiChar;
					} );
					content = $postBox.html();
					content = content.replace( /<br>|<div>/gi, '\n' ).replace( /<\/div>/gi, '' );
				} else {
					content = '';
				}

				groupID = $('#whats-new-post-in').length ? $('#whats-new-post-in').val() : BPAPRJSData.groupID;
				$.post(ajaxurl, {
					action: 'bpfb_update_activity_contents',
					data: params,
					content: content,
					group_id: groupID
				}, function (data) {

					if (data.hasOwnProperty('success') && !data.success) {
						var $action_container = $this.parents('.bpfb_action_container'),
							$feedback = $action_container.find('p.bpfb_feedback');

						if (!$feedback.length) {
							$action_container.prepend('<p class="bpfb_feedback"></p>');
							$feedback = $action_container.find('p.bpfb_feedback');
						}

						$feedback.text(data.data.message);
						return;
					}

					_bpfbActiveHandler.destroy();
					// reset content.
					if ( $postBox.is( 'textarea' ) ) {
						$text.val( '' );
					} else if ( $postBox.is( 'div' ) ) {
						$postBox.html( '' );
					}

					if ( $list.length ) {
						$list.prepend( data.activity );
					} else {
						$stream.prepend( data.activity );
					}
					/**
					 * Handle image scaling in previews.
					 */
					$( '.bpfb_final_link img' ).each( function() {
						$( this ).width( $( this ).parents( 'div' ).width() );
					} );
				} );
			} );
			$( document ).on( 'click', '#bpfb_cancel', function() {
				$( '.bpfb_toolbarItem.bpfb_active' ).removeClass( 'bpfb_active' );
				_bpfbActiveHandler.destroy();
			} );
		}

		// Only initialize if we're supposed to.
		/*
if (
	!('ontouchstart' in document.documentElement)
	||
	('ontouchstart' in document.documentElement && (/iPhone|iPod|iPad/i).test(navigator.userAgent))
	) {
	if ($("#whats-new-form").is(":visible")) setup();
}
*/
		// Meh, just do it - newish Droids seem to work fine.
		if ( $( '#whats-new-form' ).is( ':visible' ) ) {
			if( typeof BPAPRJSData.show_upload_buttons !="undefined" && parseInt(BPAPRJSData.show_upload_buttons, 10) > 0 ){
				setup();
			}
		}

		/**
		 * Handle image scaling in previews.
		 */
		$( '.bpfb_final_link img' ).each( function() {
			$( this ).width( $( this ).parents( 'div' ).width() );
		} );
	} );
}( jQuery ) );
