package com.dhanuka.indhan.DataBase.TableDataModel

/**
 * Created by John on 5/5/2018.
 */
class UserModel {
    var id: Int
    var username: String
    var profile_pic: String
    var email: String
    var phone: String
    var designation_code: String

    constructor(
        id: Int,
        username: String,
        profile_pic: String,
        email: String,
        phone: String,
        designation_code: String
    ) {
        this.id = id
        this.username = username
        this.profile_pic = profile_pic
        this.email = email
        this.phone = phone
        this.designation_code = designation_code
    }
}