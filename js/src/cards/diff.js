jQuery(document).ready(($) => {
	let currentCard;
	let oldCards;

	let currentVersion;
	let currentColumns;

	function getChildren(item) {
		let children = false;

		$(item)
			.children()
			.each(function () {
				const tag = $(this).prop('tagName');

				if ('DIV' === tag || 'UL' === tag || 'OL' === tag || 'FIGURE' === tag || 'PICTURE' === tag) {
					children = getChildren(this, children);
				} else {
					children = $(item).children();
				}
			});

		return children;
	}

	function handleLi(item, oldColumn, currentId, appendedTexts, versionNumber) {
		const children = $(item).children();
		const oldItem = $(oldColumn).find(`[data-topten-id="${currentId}"]`);

		if (children.length > 0) {
			// Has childs
			const listElements = [];

			$(children).each(function () {
				if ($(this).is('ol') || $(this).is('ul')) {
					listElements.push(this);
				}
			});

			if (listElements.length > 0) {
				listElements.forEach(function (listItem) {
					// Handle lists
					const listChildren = getChildren(listItem);

					listChildren.each(function () {
						const liId = $(this).data('topten-id');

						handleLi(this, oldColumn, liId, appendedTexts, versionNumber);
					});
				});

				// Create a clone of the current item and remove all ul and ol elements
				const clone = $(item).clone().find('ul, ol').remove().end();
				const oldClone = $(oldItem).clone().find('ul, ol').remove().end();

				const currentText = $(clone).text().trim();
				const oldText = $(oldClone).text().trim();

				if (currentText !== oldText) {
					$(item).addClass('changed');

					if (!appendedTexts.includes(oldText)) {
						let change = '<div class="change-wrapper">';
						change +=
							'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
							versionNumber +
							'</p>';

						const oldItemHtml = $(oldItem).html();

						change += '<div class="change-text"><p>' + oldItemHtml + '</p></div>';
						change += '</div>';

						$(item).after(change);

						appendedTexts.push(oldText);
					}
				}
			}
		} else {
			// No childs
			findChanges(oldColumn, currentId, item, appendedTexts, versionNumber);
		}
	}

	function handleLiOld(oldItem, currentColumn, oldId, appendedTexts, versionNumber, parent = null) {
		const children = $(oldItem).children();
		const currentItem = $(currentColumn).find(`[data-topten-id="${oldId}"]`);

		if (currentItem.length === 0) {
			// Add empty element
			const emptyElement = $('<li class="empty-line"></li>');

			// Append empty element
			$(parent).append(emptyElement);
		} else if (children.length > 0) {
			// Has childs
			const listElements = [];

			$(children).each(function () {
				if ($(this).is('ol') || $(this).is('ul')) {
					listElements.push(this);
				}
			});

			if (listElements.length > 0) {
				listElements.forEach(function (listItem) {
					// Handle lists
					const listChildren = getChildren(listItem);

					listChildren.each(function () {
						const liId = $(this).data('topten-id');
						const currentLi = $(currentColumn).find(`[data-topten-id="${liId}"]`);

						if (currentLi.length === 0) {
							const parentId = $(this).parent().data('topten-id');
							parent = $(currentColumn).find(`[data-topten-id="${parentId}"]`);

							const oldHtml = $(this).html();

							let change = '<div class="change-wrapper">';
							change +=
								'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
								versionNumber +
								'</p>';
							change += '<div class="change-text"><p>' + oldHtml + '</p></div>';
							change += '</div>';

							const emptyElement = $('<li><div class="empty-line"></div></li>');

							emptyElement.append(change);

							// Append empty element
							$(parent).append(emptyElement);
						} else {
							parent = $(currentLi).parent();

							handleLiOld(this, currentColumn, liId, appendedTexts, versionNumber, parent);
						}
					});
				});
			}
		}
	}

	function findChanges(oldColumn, currentId, currentItem, appendedTexts, versionNumber) {
		const oldItem = $(oldColumn).find(`[data-topten-id="${currentId}"]`);

		if (oldItem.length > 0) {
			const oldText = $(oldItem).text().trim();
			const currentText = $(currentItem).text().trim();

			if (currentText !== oldText) {
				$(currentItem).addClass('changed');

				if (!appendedTexts.includes(oldText)) {
					let change = '<div class="change-wrapper">';
					change +=
						'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
						versionNumber +
						'</p>';

					const oldItemHtml = $(oldItem).html();

					change += '<div class="change-text"><p>' + oldItemHtml + '</p></div>';
					change += '</div>';

					$(currentItem).after(change);

					appendedTexts.push(oldText);
				}
			}
		} else {
			markAdded(currentId, currentItem);
		}
	}

	function markAdded(currentId, currentItem) {
		$(currentItem).addClass('changed');

		let revisionVersion = '';
		// Find the oldest card that has this block
		$(oldCards).each(function () {
			const found = $(this).find(`[data-block-id="${currentId}"]`);
			if (!found.length) {
				return;
			}
			revisionVersion = $(this).parent().parent().data('version');
		});

		if (!revisionVersion) {
			revisionVersion = currentVersion;
		}

		let change = '<div class="change-wrapper">';
		change +=
			'<p class="change-header"><span class="material-symbols" aria-hidden="true">add</span>Versio ' +
			revisionVersion +
			'</p>';
		change += '</div>';

		$(currentItem).after(change);
	}

	function findChangesOld(currentColumn, oldId, currentChildren, appendedTexts, versionNumber, index, $this) {
		const currentItem = $(currentColumn).find(`[data-topten-id="${oldId}"]`);

		if (currentItem.length === 0) {
			const itemAtIndex = $(currentChildren).eq(index);

			const oldText = $($this).text().trim();
			const oldItemHtml = $($this).html();

			let change = '<div class="change-wrapper">';
			change +=
				'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
				versionNumber +
				'</p>';
			change += '<div class="change-text"><p>' + oldItemHtml + '</p></div>';
			change += '</div>';

			if (itemAtIndex.length > 0) {
				if (!appendedTexts.includes(oldText)) {
					// There is an item at the index
					const emptyElement = $('<div class="empty-line"></div>');

					$(itemAtIndex).before(emptyElement);
					$(itemAtIndex).before(change);
					appendedTexts.push(oldText);
				}
			} else {
				// Create empty element
				const emptyElement = $('<div class="empty-line"></div>');

				// Use last child instead
				const lastChild = $(currentColumn).children().last();

				// Append empty element
				$(lastChild).after(emptyElement);

				// Append change
				$(emptyElement).after(change);
			}
		}
	}

	function handlePrefixOrSuffix(prefix, oldPrefix, isPrefix, versionNumber, appendedTexts, currentColumn) {
		if (prefix.length > 0) {
			// Prefix exists

			if (oldPrefix.length === 0) {
				// Old prefix does not exist
				$(prefix).addClass('changed');
			} else {
				const prefixText = $(prefix).text().trim();
				const oldPrefixText = $(oldPrefix).text().trim();

				if (prefixText !== oldPrefixText) {
					// Text has changed
					const oldHtml = $(oldPrefix).html();

					$(prefix).addClass('changed');

					let change = '<div class="change-wrapper">';
					change +=
						'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
						versionNumber +
						'</p>';
					change += '<div class="change-text"><p>' + oldHtml + '</p></div>';
					change += '</div>';

					$(prefix).after(change);

					appendedTexts.push(oldPrefixText);
				}
			}
		} else if (oldPrefix.length > 0) {
			// Prefix does not exist, but old prefix exists
			// Add empty element
			const emptyElement = $('<div class="empty-suffix"></div>');

			// Use last child instead

			const inputId = $(oldPrefix).parent().find('input').attr('id');
			const currentInput = $(currentColumn).find(`#${inputId}`);

			if (isPrefix) {
				$(currentInput).parent().prepend(emptyElement);
			} else {
				$(currentInput).parent().append(emptyElement);
			}
		}
	}

	if ($('body').hasClass('page-template-template-vertaa')) {
		currentCard = $('.card-content.current');
		oldCards = $('.card-content.old');

		currentVersion = $(currentCard).parent().parent().data('version');
		currentColumns = $(currentCard).find('.row-block:not(.top) .column-item');

		currentColumns.each(function () {
			const currentColumn = this;
			const blockId = $(currentColumn).data('block-id');
			const currentChildren = getChildren(currentColumn);
			const appendedTexts = [];

			$(oldCards).each(function () {
				const versionNumber = $(this).parent().parent().data('version');
				const oldColumn = $(this).find(`[data-block-id="${blockId}"]`);

				if (oldColumn.length > 0) {
					if ($(currentColumn).hasClass('image-wrapper')) {
						const currentImg = $(currentColumn).find('img');
						const currentSrc = $(currentImg).attr('src').trim();

						const oldImg = $(oldColumn).find('img');
						const oldSrc = $(oldImg).attr('src').trim();

						const currentCaption = $(currentColumn).find('figcaption');
						const oldCaption = $(oldColumn).find('figcaption');

						const oldFigure = $('<figure></figure>');

						let hasChanges = false;

						if (currentSrc !== oldSrc) {
							$(currentImg).addClass('changed');

							if (!appendedTexts.includes(oldSrc)) {
								let change = '<div class="change-wrapper">';
								change +=
									'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
									versionNumber +
									'</p>';
								change += '<div class="change-text">' + $(oldImg)[0].outerHTML + '</div>';
								change += '</div>';

								$(oldFigure).append(change);
								appendedTexts.push(oldSrc);

								hasChanges = true;
							}
						}

						if (currentCaption.length > 0 && oldCaption.length > 0) {
							const currentCaptionText = $(currentCaption).text().trim();
							const oldCaptionText = $(oldCaption).text().trim();

							if (currentCaptionText !== oldCaptionText) {
								$(currentCaption).addClass('changed');

								hasChanges = true;
							}
						} else if (currentCaption.length > 0) {
							$(currentCaption).addClass('changed');
						}

						if (hasChanges) {
							$(currentColumn).append(oldFigure);
						}
					}

					// Block existed in this revision
					const oldChildren = getChildren(oldColumn);

					$(currentChildren).each(function () {
						const currentId = $(this).data('topten-id');
						const currentItem = this;
						const tag = $(this).prop('tagName');

						if (!currentId) {
							if ('LABEL' === tag) {
								const currentInput = $(this).find('input');
								const currentInputId = $(currentInput).attr('id');

								const oldInput = $(oldColumn).find(`#${currentInputId}`);

								// If input does not exist
								if (oldInput.length === 0) {
									$(currentInput).addClass('changed');
								} else {
									const currentLabel = $(currentInput).parent().parent().find('.label-text');
									const currentLabelText = $(currentLabel).text().trim();

									const oldLabel = $(oldInput).parent().parent().find('.label-text');
									const oldLabelText = $(oldLabel).text().trim();

									if (currentLabelText && oldLabelText) {
										if (currentLabelText !== oldLabelText) {
											$(currentLabel).addClass('changed');

											if (!appendedTexts.includes(oldLabelText)) {
												let change = '<div class="change-wrapper">';
												change +=
													'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
													versionNumber +
													'</p>';
												change += '<div class="change-text"><p>' + oldLabelText + '</p></div>';
												change += '</div>';

												$(currentLabel).parent().append(change);
												appendedTexts.push(oldLabelText);
											}
										}
									} else if (currentLabelText && !oldLabelText) {
										$(currentLabel).addClass('changed');
									} else if (!currentLabelText && oldLabelText) {
										const emptyElement = $('<div class="empty-line"></div>');

										$(currentInput).parent().parent().prepend(emptyElement);
									}
								}

								const prefix = $(this).find('.prefix');
								const oldPrefix = $(oldColumn).find('.prefix');

								const suffix = $(this).find('.suffix');
								const oldSuffix = $(oldColumn).find('.suffix');

								handlePrefixOrSuffix(prefix, oldPrefix, true, versionNumber, appendedTexts, currentColumn);
								handlePrefixOrSuffix(suffix, oldSuffix, false, versionNumber, appendedTexts, currentColumn);
							}
						} else if ('OL' === tag || 'UL' === tag) {
							const oldItem = $(oldColumn).find(`[data-topten-id="${currentId}"]`);

							if ($(oldItem).length > 0) {
								// Item exists
							} else {
								// Item doesn't exists
								markAdded(currentId, currentItem);
							}
						} else if ('LI' === tag) {
							handleLi(this, oldColumn, currentId, appendedTexts, versionNumber);
						} else if ('TABLE' === tag) {
							const children = $(this).find('tr');

							$(children).each(function () {
								const childCurrentId = $(this).data('topten-id');
								const oldItem = $(oldColumn).find(`[data-topten-id="${childCurrentId}"]`);

								if (oldItem.length > 0) {
									// Item exists
									$(this)
										.children('th, td')
										.each(function () {
											const tdId = $(this).data('topten-id');
											const tdOldItem = $(oldItem).find(`[data-topten-id="${tdId}"]`);

											if (tdOldItem.length > 0) {
												// Item exists

												const currentText = $(this).text().trim();
												const oldText = $(tdOldItem).text().trim();

												if (currentText !== oldText) {
													$(this).addClass('changed');

													if (!appendedTexts.includes(oldText)) {
														let change = '<div class="change-wrapper">';
														change +=
															'<p class="change-header"><span class="material-symbols" aria-hidden="true">compare_arrows</span>Versio ' +
															versionNumber +
															'</p>';
														change += '<div class="change-text"><p>' + oldText + '</p></div>';
														change += '</div>';

														$(this).append(change);

														appendedTexts.push(oldText);
													}
												}
											}
										});
								} else {
									// Item doesn't exists
									$(this).addClass('changed');
								}
							});
						} else {
							findChanges(oldColumn, currentId, currentItem, appendedTexts, versionNumber, currentVersion);
						}
					});

					$(oldChildren).each(function (index) {
						const oldId = $(this).data('topten-id');
						const tag = $(this).prop('tagName');

						if (!oldId) {
							// No ID found, handle this case!
							// E.g. titles
						} else if ('OL' === tag || 'UL' === tag) {
							// This should never run
						} else if ('LI' === tag) {
							handleLiOld(this, currentColumn, oldId, appendedTexts, versionNumber);
						} else if ('TABLE' === tag) {
						} else {
							findChangesOld(currentColumn, oldId, currentChildren, appendedTexts, versionNumber, index, this);
						}
					});
				} else {
					// Block did not exist in this revision
					$(currentColumn).addClass('added');
				}
			});
		});
	}
});
