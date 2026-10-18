import './bootstrap';
import SysInvAdminTheme from './quill-theme';
import Quill from 'quill';
import { html as beautifyHtml } from 'js-beautify';
import MediaManager from './media-manager';
import RelationshipManager from './relationship-manager';
import RelationshipModule from './relationship-module';


SysInvAdminTheme.DEFAULTS = {
  modules: {
    toolbar: true
  }
};
Quill.register('themes/sysinvadmin', SysInvAdminTheme);
window.Quill = Quill;
window.beautifyHtml = beautifyHtml;
window.MediaManager = MediaManager;
window.RelationshipManager = RelationshipManager;
window.RelationshipModule = RelationshipModule;