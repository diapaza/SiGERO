import pluginVue from 'eslint-plugin-vue'
import vueParser from 'vue-eslint-parser'
import tseslint from 'typescript-eslint'
import globals from 'globals'

export default [
  {
    ignores: ['public/**', 'vendor/**', 'node_modules/**', 'resources/js/icons/**'],
  },
  ...pluginVue.configs['flat/recommended'],
  ...tseslint.configs.recommended,
  {
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: tseslint.parser,
        ecmaVersion: 'latest',
        sourceType: 'module',
      },
      globals: {
        ...globals.browser,
      },
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/no-v-html': 'off',
      'vue/require-default-prop': 'off',
      'vue/require-prop-types': 'off',
      '@typescript-eslint/no-explicit-any': 'warn',
      '@typescript-eslint/no-unused-vars': [
        'warn',
        { argsIgnorePattern: '^_', varsIgnorePattern: '^_' },
      ],
      '@typescript-eslint/no-empty-object-type': 'off',
      'no-console': 'warn',
      'prefer-const': 'error',
      'no-var': 'error',
    },
  },
]
