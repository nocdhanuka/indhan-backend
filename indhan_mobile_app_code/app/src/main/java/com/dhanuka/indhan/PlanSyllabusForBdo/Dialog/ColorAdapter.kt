package com.dhanuka.indhan.PlanSyllabusForBdo.Dialog

import android.content.Context
import androidx.core.content.ContextCompat
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.Filter
import android.widget.Filterable
import android.widget.TextView
import com.dhanuka.indhan.R
import java.util.*
import kotlin.collections.ArrayList

class ColorAdapter(context: Context, var itemstr: ArrayList<ColorModel>) :
    BaseAdapter(), Filterable {
    private val inflater: LayoutInflater =
        context.getSystemService(Context.LAYOUT_INFLATER_SERVICE) as LayoutInflater
    var context: Context = context
    var filterDataSource: ArrayList<ColorModel> = itemstr

    init {
        filter
    }

    override fun getCount(): Int = filterDataSource.size

    override fun getItem(position: Int): Any {
        return filterDataSource[position]
    }

    override fun getItemId(position: Int): Long {
        return position.toLong()
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        val rowView = inflater.inflate(R.layout.items_view, parent, false)
        val titleTextView = rowView.findViewById(R.id.text1) as TextView
        if (filterDataSource[position].color == "red") {
            titleTextView.setTextColor(ContextCompat.getColor(context, R.color.red))
        } else {
            titleTextView.setTextColor(ContextCompat.getColor(context, R.color.progess_green))
        }
//        titleTextView.setTextColor(ContextCompat.getColor(context, R.color.white))
        titleTextView.text = filterDataSource[position].name
        return rowView
    }

    override fun getFilter(): Filter {
        return ValueFilter()
    }

    inner class ValueFilter : Filter() {
        //Invoked in a worker thread to filter the data according to the constraint.
        override fun performFiltering(constraint: CharSequence): FilterResults {
            val results = FilterResults()
            if (constraint.isNotEmpty()) {
                val filterList: ArrayList<ColorModel> = ArrayList()
                for (i in 0 until itemstr.size) {
                    if (itemstr[i].getName().toUpperCase(Locale.ROOT)
                            .contains(constraint.toString().toUpperCase(Locale.ROOT))
                    ) {
                        val contacts = ColorModel()
                        contacts.setName(itemstr[i].getName())
                        contacts.setColor(itemstr[i].getColor())
                        filterList.add(contacts)
                    }
                }
                results.count = filterList.size
                results.values = filterList
            } else {
                results.count = itemstr.size
                results.values = itemstr
            }
            return results
        }

        //Invoked in the UI thread to publish the filtering results in the user interface.
        override fun publishResults(
            constraint: CharSequence,
            results: FilterResults
        ) {
            filterDataSource = results.values as ArrayList<ColorModel>
            notifyDataSetChanged()
        }
    }
}