package com.dhanuka.indhan.Activity.NewTicketMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/28/2018.
 */

class CropBean {

    @SerializedName("cID")
    @Expose
    private var cID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null

    fun getcID(): Int? {
        return cID
    }

    fun setcID(cID: Int?) {
        this.cID = cID
    }

}
