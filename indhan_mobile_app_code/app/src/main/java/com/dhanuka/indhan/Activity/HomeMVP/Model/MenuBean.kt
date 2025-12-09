package com.dhanuka.indhan.Activity.HomeMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/27/2018.
 */

class MenuBean {

    @SerializedName("mID")
    @Expose
    private var mID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null

    @SerializedName("tagName")
    @Expose
    var tagName: String? = null

    @SerializedName("tag_icon")
    @Expose
    var tag_icon: String? = null

    @SerializedName("base_url")
    @Expose
    var base_url: String? = null
    /* @SerializedName("dropbox_url")
     @Expose
     var dropbox_url: String? = null*/

    fun getmID(): Int? {
        return mID
    }

    fun setmID(mID: Int?) {
        this.mID = mID
    }
}
