import './bootstrap';
import SysInvAdminTheme from './quill-theme';
import Quill from 'quill';
import { html as beautifyHtml } from 'js-beautify';


SysInvAdminTheme.DEFAULTS = {
  modules: {
    toolbar: true
  }
};
Quill.register('themes/sysinvadmin', SysInvAdminTheme);
window.Quill = Quill;
window.beautifyHtml = beautifyHtml;