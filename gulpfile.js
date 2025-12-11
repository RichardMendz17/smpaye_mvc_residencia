import { src, dest, watch, series } from 'gulp'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'
import terser from 'gulp-terser'
import plumber from 'gulp-plumber'
import fs from 'fs'
import path from 'path'
import { glob } from 'glob'
import sharp from 'sharp'

const sass = gulpSass(dartSass)

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    img: 'src/img/**/*.{png,jpg,jpeg,svg}'
}

export function css(done) {
    src(paths.scss, { sourcemaps: true })
        .pipe(plumber(err => {
            console.error('Error de Sass:', err.message)
            this.emit('end')
        }))
        .pipe(sass({ outputStyle: 'compressed' }))
        .pipe(dest('./public/build/css', { sourcemaps: '.' }))
    done()
}

export function js(done) {
    src(paths.js)
        .pipe(terser())
        .pipe(dest('./public/build/js'))
    done()
}

export async function imagenes(done) {
    const srcDir = './src/img'
    const buildDir = './public/build/img'
    const images = await glob(paths.img)

    images.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file))
        const outputSubDir = path.join(buildDir, relativePath)
        procesarImagenes(file, outputSubDir)
    })

    done()
}

function procesarImagenes(file, outputSubDir) {
    if (!fs.existsSync(outputSubDir)) {
        fs.mkdirSync(outputSubDir, { recursive: true })
    }

    const baseName = path.basename(file, path.extname(file))
    const extName = path.extname(file).toLowerCase()

    if (extName === '.svg') {
        const outputFile = path.join(outputSubDir, `${baseName}${extName}`)
        fs.copyFileSync(file, outputFile)
    } else {
        const outputFile = path.join(outputSubDir, `${baseName}${extName}`)
        const outputFileWebp = path.join(outputSubDir, `${baseName}.webp`)
        const outputFileAvif = path.join(outputSubDir, `${baseName}.avif`)
        const options = { quality: 80 }

        sharp(file).jpeg(options).toFile(outputFile)
        sharp(file).webp(options).toFile(outputFileWebp)
        sharp(file).avif().toFile(outputFileAvif)
    }
}

export function dev() {
    watch(paths.scss, css)
    watch(paths.js, js)
    watch(paths.img, imagenes)
}

export default series(js, css, imagenes, dev)
