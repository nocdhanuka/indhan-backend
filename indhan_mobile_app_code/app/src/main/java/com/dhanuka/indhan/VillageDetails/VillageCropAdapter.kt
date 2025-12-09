package com.dhanuka.indhan.VillageDetails

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemVillageCropBinding


class VillageCropAdapter(
    var context: Context,
    var list: MutableList<VillageDetailsModel.Data.Crop>
) : RecyclerView.Adapter<VillageCropAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemVillageCropBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_village_crop, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemVillageCropBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtCrop.text = list[position].crop
            view.txtNoOfAcres.text = list[position].acres.toString()
            view.txtFarmers.text = list[position].farmersCount


        }


    }
}