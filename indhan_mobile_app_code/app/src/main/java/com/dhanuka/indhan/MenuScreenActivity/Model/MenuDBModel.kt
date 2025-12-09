package com.dhanuka.indhan.MenuScreenActivity.Model

/**
 * Created by John on 5/3/2018.
 */
class MenuDBModel {
    var id: String
    var idInt: Int = 0
    var name: String
    var target: String
    var image_url: String?
    var notification_count: Int
    var menu_show: Int

    constructor(
        id: String,
        name: String,
        target: String,
        image_url: String?,
        notification_count: Int,
        menu_show: Int
    ) {
        this.id = id
        this.name = name
        this.target = target
        this.image_url = image_url
        this.notification_count = notification_count
        this.menu_show = menu_show
        this.idInt = 0
    }
}
