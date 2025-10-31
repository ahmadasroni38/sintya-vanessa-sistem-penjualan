const { execSync } = require('child_process');
const fs = require('fs');

// Read package.json
const pkg = JSON.parse(fs.readFileSync('package.json', 'utf8'));

// Install each devDependency
Object.keys(pkg.devDependencies).forEach(dep => {
  const version = pkg.devDependencies[dep];
  console.log(`Installing ${dep}@${version}...`);
  try {
    execSync(`npm install ${dep}@${version} --save-dev`, { stdio: 'inherit' });
  } catch (e) {
    console.error(`Failed to install ${dep}`);
  }
});
