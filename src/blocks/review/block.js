import icon from './icon';
import './style.scss';
import './editor.scss';

import { ReviewBody } from './components';
import {
	version_1_1_2,
	version_1_1_4,
	version_1_1_5,
	updateFrom
} from './oldVersions';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { BlockControls, InspectorControls, PanelColorSettings } = wp.editor;

const { Toolbar, IconButton, FormToggle, PanelBody, PanelRow } = wp.components;
const { withState, compose } = wp.compose;
const { withSelect } = wp.data;

const attributes = {
	ID: {
		type: 'string',
		default: ''
	},
	blockID: {
		type: 'string',
		default: ''
	},
	authorName: {
		type: 'string',
		default: ''
	},
	itemName: {
        type: 'string',
        default: ''
	},
	items: {
		type: 'string',
		default: '[{"label":"","value":0}]'
	},
	parts: {
		type: 'array',
		default: [{ label: '', value: 0 }]
	},
	starCount: {
		type: 'number',
		default: 5
	},
	summaryTitle: {
		type: 'string',
		default: 'Summary'
	},
	summaryDescription: {
        type: 'string',
        default: ''
	},
	callToActionText: {
        type: 'string',
        default: ''
	},
	callToActionURL: {
		type: 'string',
		default: ''
	},
	callToActionBackColor: {
		type: 'string',
		default: '#f63d3d'
	},
	callToActionForeColor: {
		type: 'string',
		default: '#ffffff'
	},
	inactiveStarColor: {
		type: 'string',
		default: '#888888'
	},
	activeStarColor: {
		type: 'string',
		default: '#eeee00'
	},
	selectedStarColor: {
		type: 'string',
		default: '#ffff00'
	},
	titleAlign: {
		type: 'string',
		default: 'left'
	},
	authorAlign: {
		type: 'string',
		default: 'left'
	},
	enableCTA: {
		type: 'boolean',
		default: true
	},
	ctaNoFollow: {
		type: 'boolean',
		default: true
	},
	ctaOpenInNewTab: {
		type: 'boolean',
		default: true
	}
};

registerBlockType('ub/review', {
	title: __('Review'),
	icon: icon,
	category: 'ultimateblocks',
	keywords: [__('Review'), __('Ultimate Blocks')],
	attributes,
	edit: compose([
		withState({ editable: '' }),
		withSelect((select, ownProps) => ({
			block: select('core/editor').getBlock(ownProps.clientId)
		}))
	])(function(props) {
		const { setAttributes, isSelected, editable, setState, block } = props;
		const {
			blockID,
			authorName,
			itemName,
			items,
			parts,
			starCount,
			summaryTitle,
			summaryDescription,
			callToActionText,
			callToActionURL,
			callToActionBackColor,
			callToActionForeColor,
			inactiveStarColor,
			activeStarColor,
			selectedStarColor,
			titleAlign,
			authorAlign,
			enableCTA,
			ctaNoFollow,
			ctaOpenInNewTab
		} = props.attributes;

		if (blockID !== block.clientId) {
			setAttributes({
				blockID: block.clientId
			});
		}

		const setAlignment = (target, value) => {
			switch (target) {
				case 'reviewTitle':
					setAttributes({ titleAlign: value });
					break;
				case 'reviewAuthor':
					setAttributes({ authorAlign: value });
					break;
			}
		};

		const getCurrentAlignment = target => {
			switch (target) {
				case 'reviewTitle':
					return titleAlign;
				case 'reviewAuthor':
					return authorAlign;
			}
		};

		if (
			items &&
			items !== JSON.stringify(parts) &&
			parts.length === 1 &&
			parts[0].label === '' &&
			parts[0].value === 0
		) {
			setAttributes({
				parts: JSON.parse(items),
				items: '[{"label":"","value":0}]'
			});
		}

		return [
			isSelected && (
				<InspectorControls>
					<PanelColorSettings
						title={__('Star Colors')}
						initialOpen={true}
						colorSettings={[
							{
								value: activeStarColor,
								onChange: colorValue =>
									setAttributes({
										activeStarColor: colorValue
									}),
								label: __('Active Star Color')
							},
							{
								value: inactiveStarColor,
								onChange: colorValue =>
									setAttributes({
										inactiveStarColor: colorValue
									}),
								label: __('Inactive Star Color')
							},
							{
								value: selectedStarColor,
								onChange: colorValue =>
									setAttributes({
										selectedStarColor: colorValue
									}),
								label: __('Selected Star Color')
							}
						]}
					/>
					<PanelColorSettings
						title={__('Button Colors')}
						initialOpen={false}
						colorSettings={[
							{
								value: callToActionBackColor,
								onChange: colorValue =>
									setAttributes({
										callToActionBackColor: colorValue
									}),
								label: __('Button Background')
							},
							{
								value: callToActionForeColor,
								onChange: colorValue =>
									setAttributes({
										callToActionForeColor: colorValue
									}),
								label: __('Button Text Color')
							}
						]}
					/>
					<PanelBody
						title={__('Call to Action button')}
						initialOpen={true}
					>
						<PanelRow>
							<label htmlFor="ub-review-cta-enable">
								{__('Enable')}
							</label>
							<FormToggle
								id="ub-review-cta-enable"
								label={__('Enable')}
								checked={enableCTA}
								onChange={_ =>
									setAttributes({ enableCTA: !enableCTA })
								}
							/>
						</PanelRow>
						{enableCTA && (
							<React.Fragment>
								<PanelRow>
									<label htmlFor="ub-review-cta-nofollow">
										{__('Add nofollow')}
									</label>
									<FormToggle
										id="ub-review-cta-nofollow"
										label={__('Add nofollow')}
										checked={ctaNoFollow}
										onChange={_ =>
											setAttributes({
												ctaNoFollow: !ctaNoFollow
											})
										}
									/>
								</PanelRow>
								<PanelRow>
									<label htmlFor="ub-review-cta-openinnewtab">
										{__('Open link in new tab')}
									</label>
									<FormToggle
										id="ub-review-cta-openinnewtab"
										label={__('Open link in new tab')}
										checked={ctaOpenInNewTab}
										onChange={_ =>
											setAttributes({
												ctaOpenInNewTab: !ctaOpenInNewTab
											})
										}
									/>
								</PanelRow>
							</React.Fragment>
						)}
					</PanelBody>
				</InspectorControls>
			),
			isSelected && (
				<BlockControls>
					{editable !== '' && (
						<Toolbar>
							{['left', 'center', 'right', 'justify'].map(a => (
								<IconButton
									icon={`editor-${
										a === 'justify' ? a : 'align' + a
									}`}
									label={__(
										(a !== 'justify' ? 'Align ' : '') +
											a[0].toUpperCase() +
											a.slice(1)
									)}
									isActive={
										getCurrentAlignment(editable) === a
									}
									onClick={() => setAlignment(editable, a)}
								/>
							))}
						</Toolbar>
					)}
				</BlockControls>
			),
			<ReviewBody
				authorName={authorName}
				itemName={itemName}
				ID={blockID}
				items={parts}
				starCount={starCount}
				summaryTitle={summaryTitle}
				summaryDescription={summaryDescription}
				callToActionText={callToActionText}
				callToActionURL={callToActionURL}
				callToActionBackColor={callToActionBackColor}
				callToActionForeColor={callToActionForeColor}
				inactiveStarColor={inactiveStarColor}
				activeStarColor={activeStarColor}
				selectedStarColor={selectedStarColor}
				setAuthorName={newValue =>
					setAttributes({ authorName: newValue })
				}
				setItemName={newValue => setAttributes({ itemName: newValue })}
				setItems={newValue => setAttributes({ parts: newValue })}
				setSummaryTitle={newValue =>
					setAttributes({ summaryTitle: newValue })
				}
				setSummaryDescription={newValue =>
					setAttributes({ summaryDescription: newValue })
				}
				setCallToActionText={newValue =>
					setAttributes({ callToActionText: newValue })
				}
				setCallToActionURL={newValue =>
					setAttributes({ callToActionURL: newValue })
				}
				hasFocus={isSelected}
				setEditable={newValue => setState({ editable: newValue })}
				alignments={{ titleAlign, authorAlign }}
				enableCTA={enableCTA}
				ctaNoFollow={ctaNoFollow}
			/>
		];
	}),
	save: () => null,
	deprecated: [
		updateFrom(version_1_1_2),
		updateFrom(version_1_1_4),
		updateFrom(version_1_1_5)
	]
});
