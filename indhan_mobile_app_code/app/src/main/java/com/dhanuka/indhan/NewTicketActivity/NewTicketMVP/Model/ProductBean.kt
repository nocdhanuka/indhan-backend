package com.dhanuka.indhan.Activity.NewTicketMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/28/2018.
 */

class ProductBean {
    @SerializedName("pID")
    @Expose
    private var pID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null

    @SerializedName("crop_id")
    @Expose
    var crop_id: String? = null
        get() = field
        set(value) {
            field = value
        }

    fun getpID(): Int? {
        return pID
    }

    fun setpID(pID: Int?) {
        this.pID = pID
    }

}
