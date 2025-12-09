package com.dhanuka.indhan.support.model

import android.graphics.drawable.Drawable

class QuestionModel {
    var id: String
    var name: String
    var bgLayout: Drawable
    var gravity: Int
    var optionType: String

    constructor(id: String, name: String, optionType: String, gravity: Int, bgLayout: Drawable) {
        this.id = id
        this.name = name
        this.optionType = optionType
        this.gravity = gravity
        this.bgLayout = bgLayout
    }
}