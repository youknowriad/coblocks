/**
 * External dependencies
 */
import get from 'lodash/get';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './styles/editor.scss';
import './styles/style.scss';
import Edit from './components/edit';
import icons from './../../utils/icons';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { createBlock } = wp.blocks;
const { RichText, getColorClassName, getFontSizeClass } = wp.editor;

/**
 * Block constants
 */
const name = 'click-to-tweet';

const title = __( 'Click to Tweet' );

const icon = icons.twitter;

const keywords = [
	__( 'social' ),
	__( 'twitter' ),
	__( 'coblocks' ),
];

const blockAttributes = {
	content: {
		type: 'array',
		source: 'children',
		selector: 'p',
		default: [],
	},
	url: {
		type: 'attribute',
	},
	textAlign: {
		type: 'string',
	},
	via: {
		type: 'string',
	},
	buttonText: {
		type: 'string',
		default: __( 'Click to Tweet' ),
	},
	buttonColor: {
		type: 'string',
	},
	textColor: {
		type: 'string',
	},
	customButtonColor: {
		type: 'string',
	},
	customTextColor: {
		type: 'string',
	},
	fontSize: {
		type: 'string',
	},
	customFontSize: {
		type: 'number',
	},
};

const settings = {

	title: title,

	description: __( 'Add a quote for readers to tweet via Twitter.' ),

	icon: {
		src: icon,
	},

	keywords: keywords,

	attributes: blockAttributes,

	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'core/paragraph' ],
				transform: ( { content } ) => {
					return createBlock( 'coblocks/click-to-tweet', { content: content } );
				},
			},
			{
				type: 'block',
				blocks: [ 'core/quote' ],
				transform: ( { value, citation } ) => {
					// transforming an empty click to share element
					if ( ( ! value || ! value.length ) && ! citation ) {
						return createBlock( 'coblocks/click-to-tweet' );
					}
					// transforming a click to share element with content
					return ( value || [] ).map( item => createBlock( 'coblocks/click-to-tweet', {
						content: [ get( item, 'children.props.children', '' ) ],
					} ) ).concat( citation ? createBlock( 'core/paragraph', {
						content: citation,
					} ) : [] );
				},
			},
			{
				type: 'block',
				blocks: [ 'core/pullquote' ],
				transform: ( { value, citation } ) => {
					// transforming an empty click to share element
					if ( ( ! value || ! value.length ) && ! citation ) {
						return createBlock( 'coblocks/click-to-tweet' );
					}
					// transforming a click to share element with content
					return ( value || [] ).map( item => createBlock( 'coblocks/click-to-tweet', {
						content: [ get( item, 'children.props.children', '' ) ],
					} ) ).concat( citation ? createBlock( 'core/paragraph', {
						content: citation,
					} ) : [] );
				},
			},
			{
				type: 'raw',
				selector: 'blockquote.wp-block-coblocks-click-to-tweet',
				schema: {
					blockquote: {
						classes: [ 'wp-block-coblocks-click-to-tweet' ],
					},
				},
			},
		],
		to: [
			{
				type: 'block',
				blocks: [ 'core/paragraph' ],
				transform: ( { content } ) => {
					// transforming an empty click to share element
					if ( ! content || ! content.length ) {
						return createBlock( 'core/paragraph' );
					}
					// transforming a click to share element with content
					return ( content || [] ).map( item => createBlock( 'core/paragraph', {
						content: content,
					} ) );
				},
			},
			{
				type: 'block',
				blocks: [ 'core/quote' ],
				transform: ( { content } ) => {
					// transforming a click to share element with content
					return createBlock( 'core/quote', {
						value: [
							{ children: <p key="1">{ content }</p> },
						],
					} );
				},
			},
			{
				type: 'block',
				blocks: [ 'core/pullquote' ],
				transform: ( { content } ) => {
					// transforming a click to share element with content
					return createBlock( 'core/pullquote', {
						value: [
							{ children: <p key="1">{ content }</p> },
						],
					} );
				},
			},
		],
	},

	edit: Edit,

	save( { attributes } ) {

		const {
			buttonColor,
			buttonText,
			customButtonColor,
			customTextColor,
			content,
			customFontSize,
			fontSize,
			textColor,
			textAlign,
			url,
			via,
		} = attributes;

		const viaUrl = via ? `&via=${via}` : '';

		const tweetUrl = `http://twitter.com/share?&text=${ encodeURIComponent( content ) }&url=${url}${viaUrl}`;

		const textColorClass = getColorClassName( 'color', textColor );

		const fontSizeClass = getFontSizeClass( fontSize );

		const textClasses = classnames( 'wp-block-coblocks-click-to-tweet__text', {
			'has-text-color': textColor || customTextColor,
			[ fontSizeClass ]: fontSizeClass,
			[ textColorClass ]: textColorClass,
		} );

		const textStyles = {
			fontSize: fontSizeClass ? undefined : customFontSize,
			color: textColorClass ? undefined : customTextColor,
		};

		const buttonColorClass = getColorClassName( 'background-color', buttonColor );

		const buttonClasses = classnames( 'wp-block-coblocks-click-to-tweet__twitter-btn', {
			'has-button-color':  buttonColor || customButtonColor,
			[ buttonColorClass ]: buttonColorClass,
		} );

		const buttonStyles = {
			backgroundColor: buttonColorClass ? undefined : customButtonColor,
		};

		if ( content && content.length > 0 ) {
			return (
				<blockquote style={ { textAlign: textAlign } }>
					<RichText.Content
						tagName="p"
						className={ textClasses }
						style={ textStyles }
						value={ content }
					/>

					<RichText.Content
						tagName="a"
						className={ buttonClasses }
						style={ buttonStyles }
						value={ buttonText }
						href={ tweetUrl }
						target="_blank"
					/>
				</blockquote>
			);
		}

		return null;
	},
};

export { name, title, icon, settings };
