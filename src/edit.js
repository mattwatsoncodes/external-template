import { BlockContextProvider, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

function PostTemplateInnerBlocks() {
	/**
	 * Adding an initial block so that we can see the template.
	 */
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'wp-block-post' },
		{ template: [
			[ 'external-meta-block/external-meta-block', { tagName: 'h3' } ]
		] }
	);
	return <li { ...innerBlocksProps } />;
}

export default function Edit( { context: { endpoint, posts } } ) {
	let [ externalPosts, setExternalPosts ] = useState( [] );

	useEffect( () => {
		let mounted = true;

		if ( ! endpoint || externalPosts.length > 0 ) {
			return () => mounted = false;
		}

		/**
		 * We will need a caching layer here so it does not
		 * get the data on every editor reload load.
		 */
		fetch( endpoint, {
			referrerPolicy: 'origin'
		})
		.then( response => response.json() )
		.then( posts => {
			if ( mounted ) {
				setExternalPosts( posts );
			}
		});

		return () => mounted = false;
	}, [ endpoint ] );

	// If no data is present we should output a placeholder block.
	return (
		<ul { ...useBlockProps() }>
			{ externalPosts.map( ( post ) => (
				<>
					{ /* This is what passes the context through to the inner blocks */ }
					<BlockContextProvider
						key={ post.id /* Making assumptions its coming from the REST API, perhaps make this more generic. */ }
						value={ { 'post': { ...post } } }
					>
						<PostTemplateInnerBlocks />
					</BlockContextProvider>
				</>
			) ) }
		</ul>
	);
}
