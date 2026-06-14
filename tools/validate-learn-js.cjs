const fs = require('fs');
const vm = require('vm');
const html = fs.readFileSync('tools/learn-test.html', 'utf8');
const scripts = [...html.matchAll(/<script(?![^>]*type="application\/json")[^>]*>([\s\S]*?)<\/script>/g)].map(m => m[1]);
for (let i = 0; i < scripts.length; i++) {
  try {
    new Function(scripts[i]);
  } catch (e) {
    console.log('SYNTAX_ERROR script', i, e.message);
    process.exit(1);
  }
}
console.log('ALL_SCRIPTS_OK count=' + scripts.length);

const ctx = {
  window: { __learnPremiumMixin: { initLearnPage: function() {} } },
  document: {
    getElementById: (id) => {
      if (id === 'learn-lectures-data') return { textContent: '{"10":{"id":10,"recording_url":"https://youtube.com/watch?v=test","title":"t"}}' };
      return null;
    }
  },
  console: console
};
const setup = scripts.filter(s => s.includes('__learnPremiumMixin') || s.includes('function courseFocusMode')).join('\n');
vm.runInNewContext(setup, ctx);
const data = ctx.courseFocusMode();
console.log('returns object:', data && typeof data === 'object');
console.log('loadLecture:', typeof data.loadLecture);
console.log('initLearnPage:', typeof data.initLearnPage);
