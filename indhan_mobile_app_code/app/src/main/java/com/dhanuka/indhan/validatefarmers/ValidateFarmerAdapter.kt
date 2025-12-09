package com.dhanuka.indhan.validatefarmers

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemValidateFarmerBinding


class ValidateFarmerAdapter(
    var context: Context,
    var list: MutableList<ValidatedFarmerBean.Data>,
    var listenenr: ValidationListener
) : RecyclerView.Adapter<ValidateFarmerAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemValidateFarmerBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_validate_farmer, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.setData(position)
    }

    inner class ViewHolder(var view: ItemValidateFarmerBinding) :
        RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtVillageName.text = list[position].farmerName

            if (!list[position].mobile.equals("")) {
                view.txtNoOfFarmers.text = list[position].mobile.replaceFirst(
                    list[position].mobile.substring(0, 4),
                    "****"
                )
            }

            if (list[position].callStatus == "0") {
                view.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_ylw))
            } else {
                view.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_green))
            }

            if (list[position].leadStatus == "0") {
                view.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_grey))
            } else {
                view.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_green))

            }

            view.imgCall.setOnClickListener {
                listenenr.onCall(position)
            }

            view.imgSetting.setOnClickListener {
                listenenr.onChanageStatus(position)
            }
        }
    }
}