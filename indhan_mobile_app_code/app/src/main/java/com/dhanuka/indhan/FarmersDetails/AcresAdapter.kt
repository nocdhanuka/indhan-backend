package com.dhanuka.indhan.FarmersDetails

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemFarmersAcresBinding
import com.dhanuka.indhan.farmerlist.FarmerBean


class AcresAdapter(var context: Context, var list: MutableList<FarmerBean.VillageFarmerData.FarmerProduct>) :
    RecyclerView.Adapter<AcresAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemFarmersAcresBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_farmers_acres, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return if (list.size >= 4) {
            4
        } else {
            list.size
        }
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemFarmersAcresBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {

            view.txtCrop.text = list[position].crop
            view.txtAcres.text = list[position].acres

//


        }


    }
}