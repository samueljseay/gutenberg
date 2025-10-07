/**
 * WordPress dependencies
 */
import { privateApis as routerPrivateApis } from '@wordpress/router';
import { __ } from '@wordpress/i18n';
import { loadView } from '@wordpress/views';
import { resolveSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

/**
 * Internal dependencies
 */
import Editor from '../editor';
import SidebarNavigationScreen from '../sidebar-navigation-screen';
import SidebarNavigationScreenUnsupported from '../sidebar-navigation-screen-unsupported';
import DataViewsSidebarContent from '../sidebar-dataviews';
import PostList from '../post-list';
import { unlock } from '../../lock-unlock';
import { PostEdit } from '../post-edit';
import { getDefaultView } from '../post-list/view-utils';

const { useLocation } = unlock( routerPrivateApis );

async function isListView( query ) {
	const { activeView = 'all' } = query;
	const postTypeObject =
		await resolveSelect( coreStore ).getPostType( 'page' );
	const view = await loadView( {
		kind: 'postType',
		name: 'page',
		slug: activeView,
		defaultView: getDefaultView( postTypeObject, activeView ),
	} );
	return view.type === 'list';
}

function MobilePagesView() {
	const { query = {} } = useLocation();
	const { canvas = 'view' } = query;

	return canvas === 'edit' ? <Editor /> : <PostList postType="page" />;
}

export const pagesRoute = {
	name: 'pages',
	path: '/page',
	areas: {
		sidebar( { siteData } ) {
			const isBlockTheme = siteData.currentTheme?.is_block_theme;
			return isBlockTheme ? (
				<SidebarNavigationScreen
					title={ __( 'Pages' ) }
					backPath="/"
					content={ <DataViewsSidebarContent postType="page" /> }
				/>
			) : (
				<SidebarNavigationScreenUnsupported />
			);
		},
		content( { siteData } ) {
			const isBlockTheme = siteData.currentTheme?.is_block_theme;
			return isBlockTheme ? <PostList postType="page" /> : undefined;
		},
		async preview( { query, siteData } ) {
			const isBlockTheme = siteData.currentTheme?.is_block_theme;
			if ( ! isBlockTheme ) {
				return undefined;
			}
			const isList = await isListView( query );
			return isList ? <Editor /> : undefined;
		},
		mobile( { siteData } ) {
			const isBlockTheme = siteData.currentTheme?.is_block_theme;
			return isBlockTheme ? (
				<MobilePagesView />
			) : (
				<SidebarNavigationScreenUnsupported />
			);
		},
		async edit( { query } ) {
			const isList = await isListView( query );
			const hasQuickEdit = ! isList && !! query.quickEdit;
			return hasQuickEdit ? (
				<PostEdit postType="page" postId={ query.postId } />
			) : undefined;
		},
	},
	widths: {
		async content( { query } ) {
			const isList = await isListView( query );
			return isList ? 380 : undefined;
		},
		async edit( { query } ) {
			const isList = await isListView( query );
			const hasQuickEdit = ! isList && !! query.quickEdit;
			return hasQuickEdit ? 380 : undefined;
		},
	},
};
