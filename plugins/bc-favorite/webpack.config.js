
const VueLoaderPlugin = require('vue-loader/lib/plugin')
const path = require('path');

module.exports = (env, argv) => {
    return {
        entry: ['@babel/polyfill', './webroot/js/src/admin/favorites/main.js'],
        output: {
            filename: 'bundle.js',
            path: path.join(__dirname, 'webroot/js')
        },
        plugins: [
            new VueLoaderPlugin()
        ],
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: [
                        {
                            loader: 'babel-loader',
                            options: {
                                presets: [['@babel/preset-env', {modules: false}]]
                            }
                        }
                    ]
                },
                {
                    test: /\.vue$/,
                    exclude: /node_modules/,
                    loader: ['vue-loader']
                }
            ]
        },
        devtool: 'source-map'
    };
};
