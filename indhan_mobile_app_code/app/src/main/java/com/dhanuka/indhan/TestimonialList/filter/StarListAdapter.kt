package com.dhanuka.indhan.TestimonialList.filter

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemStarListBinding


class StarListAdapter(
    var context: Context,
    var list: MutableList<STARBean>
) : RecyclerView.Adapter<StarListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemStarListBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_star_list, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.setData(position)

    }

    inner class ViewHolder(var view: ItemStarListBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.ratingBar.rating = list[position].rating.toFloat()

            if (list[position].isSelected) {
                view.checkbox.isChecked = true
            }

            view.checkbox.setOnCheckedChangeListener { buttonView, isChecked ->
                list[position].isSelected = !list[position].isSelected
            }
        }

    }


}