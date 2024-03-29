const { __ } = wp.i18n;

export const richTextToHTML = elem => {
	let outputString = '';

	outputString += `<${elem.type}${
		elem.type === 'a'
			? ` href='${elem.props.href}' rel='${elem.props.rel}' target='${elem.props.target}'`
			: elem.type === 'img'
			? ` style='${elem.props.style}' class='${elem.props.class}' src='${elem.props.src}' alt='${elem.props.alt}'`
			: ''
	}>`;

	elem.props.children.forEach(child => {
		outputString +=
			typeof child === 'string' ? child : richTextToHTML(child);
	});
	if (!['br', 'img'].includes(elem.type)) outputString += `</${elem.type}>`;

	return outputString;
};

export const mergeRichTextArray = input =>
	input
		.map(item => (typeof item === 'string' ? item : richTextToHTML(item)))
		.join('');

export const dashesToCamelcase = str =>
	str
		.split('-')
		.map(s => s[0].toUpperCase() + s.slice(1))
		.join('');

export const generateIcon = (selectedIcon, size) => (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		height={size}
		width={size}
		viewBox={`0, 0, ${selectedIcon.icon[0]}, ${selectedIcon.icon[1]}`}
	>
		<path fill={'currentColor'} d={selectedIcon.icon[4]} />
	</svg>
);

export const upgradeButtonLabel = __(
	'We have made some improvements to this block. Click here to upgrade the block. You will not lose any content.'
);

export const getDescendantBlocks = rootBlock => {
	let descendants = [];
	rootBlock.innerBlocks.forEach(innerBlock => {
		descendants.push(innerBlock);
		if (innerBlock.innerBlocks.length > 0) {
			descendants.push(...getDescendantBlocks(innerBlock));
		}
	});
	return descendants;
};
