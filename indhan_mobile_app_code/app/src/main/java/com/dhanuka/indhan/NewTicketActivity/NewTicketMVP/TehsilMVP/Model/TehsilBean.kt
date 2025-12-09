package com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/5/2018.
 */

class TehsilBean {
    @SerializedName("tID")
    @Expose
    private var tID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null

    fun gettID(): Int? {
        return tID
    }

    fun settID(tID: Int?) {
        this.tID = tID
    }
}
