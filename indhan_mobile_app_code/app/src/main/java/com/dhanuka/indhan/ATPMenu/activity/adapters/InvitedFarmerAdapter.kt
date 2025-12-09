package com.dhanuka.indhan.ATPMenu.activity.adapters

import android.content.Context
import android.content.Intent
import android.net.Uri

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse

import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_invited_farmer.view.*
import java.util.*

class InvitedFarmerAdapter(context: Context, atpBean: ArrayList<ValidatedfarmerListResponse.Data>) :
    RecyclerView.Adapter<InvitedFarmerAdapter.ViewHolder>() {
    var context: Context
    var atpBean: ArrayList<ValidatedfarmerListResponse.Data>

    init {
        this.context = context
        this.atpBean = atpBean
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_invited_farmer, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return atpBean.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        if (atpBean[position].contactNumber.length > 6) {
            val newName = atpBean[position].contactNumber.substring(0, 6) + "XXXX"
            holder.view.txtInvitedFarmeMobile.text = newName
        }
        holder.view.txtInvitedFarmeName.text = atpBean[position].farmerName

        holder.view.imgCall.setOnClickListener {
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${atpBean[position].contactNumber}")
            context.startActivity(intent)
        }


    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }


}