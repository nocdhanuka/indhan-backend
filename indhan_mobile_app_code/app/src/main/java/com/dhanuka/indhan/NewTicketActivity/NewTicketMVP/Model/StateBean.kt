package com.dhanuka.indhan.Activity.NewTicketMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/28/2018.
 */

class StateBean {
    @SerializedName("sID")
    @Expose
    private var sID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null

    fun getsID(): Int? {
        return sID
    }

    fun setsID(sID: Int?) {
        this.sID = sID
    }
}
