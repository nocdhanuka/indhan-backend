package com.dhanuka.indhan.TestimonialList.filter

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemDdListBinding


class DDListAdapter(
    var context: Context,
    var list: MutableList<DDBean.Data>,
    var clickListener: ViewClickListener
) : RecyclerView.Adapter<DDListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemDdListBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_dd_list, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }

    inner class ViewHolder(var view: ItemDdListBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtName.text = list[position].name

            view.checkbox.setOnCheckedChangeListener { buttonView, isChecked ->
                list[position].isSelected = !list[position].isSelected
            }
        }

    }


}