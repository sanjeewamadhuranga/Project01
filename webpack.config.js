const Encore = require("@symfony/webpack-encore");
const fs = require("fs");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

fs.readdirSync("./assets/themes/", { withFileTypes: true })
    .filter((fileOrDir) => fileOrDir.isDirectory())
    .map((directory) => directory.name)
    .filter((theme) => theme !== "shared")
    .forEach((theme) =>
        Encore.addStyleEntry(
            `themes/${theme}`,
            `./assets/themes/${theme}/theme.scss`
        )
    );

Encore.setOutputPath("public/build/")
    .setPublicPath("/build")
    .copyFiles({
        from: "./assets/themes", // Copy favicons and logos
        to: "themes/[path][name].[hash:8].[ext]",
        pattern: /^.*\.(ico|svg|png)$/,
    })
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry("app", "./assets/app.ts")
    .addEntry("themes/shared", "./assets/themes/shared/theme.ts")
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enablePostCssLoader()
    .enableSourceMaps(true) // Enabling here and later remove source maps before packing the source in CircleCI jobs
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills

    .enableSassLoader()
    .enableTypeScriptLoader()
    .enableReactPreset()
    // Disable host check for dev-server
    .configureDevServerOptions((options) => ({
        ...options,
        allowedHosts: "all",
        client: {
            webSocketURL: "ws://localhost:8080/ws",
        },
    }));

module.exports = Encore.getWebpackConfig();
